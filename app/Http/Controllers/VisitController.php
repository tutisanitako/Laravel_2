<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Appointment;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with(['appointment.patient.user', 'appointment.doctor.user', 'payment']);

        $user = auth()->user();

        // Role-based filtering
        if ($user->isDoctor()) {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('doctor_id', $user->doctor->id);
            });
        } elseif ($user->isPatient()) {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('patient_id', $user->patient->id);
            });
        }

        $visits = $query->orderBy('visit_date', 'desc')->paginate(10);

        return view('visits.index', compact('visits'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->isDoctor()) {
            abort(403);
        }

        $appointmentId = $request->query('appointment_id');
        $appointment = null;

        if ($appointmentId) {
            $appointment = Appointment::with(['patient.user', 'doctor.user'])->findOrFail($appointmentId);
            
            // Check if appointment already has a visit
            if ($appointment->visit) {
                return redirect()->route('visits.show', $appointment->visit->id)
                    ->with('error', 'This appointment already has a visit record.');
            }

            // Check if appointment is approved or completed
            if (!in_array($appointment->status, ['approved', 'completed'])) {
                return back()->with('error', 'Can only create visits for approved appointments.');
            }
        }

        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('status', 'approved')
            ->whereDoesntHave('visit')
            ->get();

        return view('visits.create', compact('appointments', 'appointment'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isDoctor()) {
            abort(403);
        }

        $validated = $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'diagnosis' => ['required', 'string'],
            'treatment' => ['required', 'string'],
            'prescription' => ['nullable', 'string'],
            'visit_date' => ['required', 'date'],
        ]);

        $appointment = Appointment::findOrFail($validated['appointment_id']);

        // Check if appointment already has a visit
        if ($appointment->visit) {
            return back()->with('error', 'This appointment already has a visit record.');
        }

        $visit = Visit::create($validated);

        // Update appointment status to completed
        $appointment->update(['status' => 'completed']);

        return redirect()->route('visits.show', $visit->id)->with('success', 'Visit created successfully.');
    }

    public function show(Visit $visit)
    {
        $user = auth()->user();

        // Authorization check
        if ($user->isPatient() && $visit->appointment->patient_id !== $user->patient->id) {
            abort(403);
        }

        if ($user->isDoctor() && $visit->appointment->doctor_id !== $user->doctor->id) {
            abort(403);
        }

        $visit->load(['appointment.patient.user', 'appointment.doctor.user', 'payment']);
        return view('visits.show', compact('visit'));
    }

    public function edit(Visit $visit)
    {
        if (!auth()->user()->isDoctor()) {
            abort(403);
        }

        if ($visit->appointment->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        return view('visits.edit', compact('visit'));
    }

    public function update(Request $request, Visit $visit)
    {
        if (!auth()->user()->isDoctor()) {
            abort(403);
        }

        if ($visit->appointment->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'diagnosis' => ['required', 'string'],
            'treatment' => ['required', 'string'],
            'prescription' => ['nullable', 'string'],
            'visit_date' => ['required', 'date'],
        ]);

        $visit->update($validated);

        return redirect()->route('visits.show', $visit->id)->with('success', 'Visit updated successfully.');
    }

    public function destroy(Visit $visit)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $visit->delete();
        return redirect()->route('visits.index')->with('success', 'Visit deleted successfully.');
    }
}