<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor.user']);

        $user = auth()->user();

        // Role-based filtering
        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        }

        // Search and filter functionality
        if ($request->has('doctor_id') && $request->doctor_id != '') {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('patient_id') && $request->patient_id != '') {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient.user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('doctor.user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(10);

        $doctors = Doctor::with('user')->get();
        $patients = Patient::with('user')->get();

        return view('appointments.index', compact('appointments', 'doctors', 'patients'));
    }

    public function create()
    {
        $user = auth()->user();
        $doctors = Doctor::with('user')->get();
        
        if ($user->isPatient()) {
            $patients = Patient::where('id', $user->patient->id)->with('user')->get();
        } else {
            $patients = Patient::with('user')->get();
        }

        return view('appointments.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string'],
        ]);

        // Check for conflicting appointments
        $doctor = Doctor::findOrFail($validated['doctor_id']);
        if ($doctor->hasConflictingAppointment($validated['appointment_date'])) {
            return back()->withErrors(['appointment_date' => 'This doctor already has an appointment at this time.'])->withInput();
        }

        Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'status' => 'pending',
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        $this->authorizeAppointmentAccess($appointment);
        
        $appointment->load(['patient.user', 'doctor.user', 'visit.payment']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $this->authorizeAppointmentAccess($appointment);

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Cannot edit completed appointments.');
        }

        $doctors = Doctor::with('user')->get();
        $patients = Patient::with('user')->get();

        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $this->authorizeAppointmentAccess($appointment);

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Cannot edit completed appointments.');
        }

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'status' => ['required', 'in:pending,approved,canceled,completed'],
            'notes' => ['nullable', 'string'],
        ]);

        // Check for conflicting appointments (excluding current appointment)
        $doctor = Doctor::findOrFail($validated['doctor_id']);
        if ($doctor->hasConflictingAppointment($validated['appointment_date'], $appointment->id)) {
            return back()->withErrors(['appointment_date' => 'This doctor already has an appointment at this time.'])->withInput();
        }

        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorizeAppointmentAccess($appointment);

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Cannot delete completed appointments.');
        }

        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }

    public function approve(Appointment $appointment)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isReceptionist() && !auth()->user()->isDoctor()) {
            abort(403);
        }

        $appointment->update(['status' => 'approved']);
        return back()->with('success', 'Appointment approved successfully.');
    }

    public function cancel(Appointment $appointment)
    {
        $this->authorizeAppointmentAccess($appointment);

        $appointment->update(['status' => 'canceled']);
        return back()->with('success', 'Appointment canceled successfully.');
    }

    public function complete(Appointment $appointment)
    {
        if (!auth()->user()->isDoctor()) {
            abort(403);
        }

        $appointment->update(['status' => 'completed']);
        return back()->with('success', 'Appointment completed successfully.');
    }

    private function authorizeAppointmentAccess(Appointment $appointment)
    {
        $user = auth()->user();

        if ($user->isPatient() && $appointment->patient_id !== $user->patient->id) {
            abort(403);
        }

        if ($user->isDoctor() && $appointment->doctor_id !== $user->doctor->id) {
            abort(403);
        }
    }
}