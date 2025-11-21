<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Visit;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_patients' => Patient::count(),
            'total_doctors' => Doctor::count(),
            'total_appointments' => Appointment::count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'approved_appointments' => Appointment::where('status', 'approved')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'total_visits' => Visit::count(),
            'total_payments' => Payment::sum('amount'),
            'unpaid_payments' => Payment::where('status', 'unpaid')->sum('amount'),
        ];

        // Role-specific data
        if ($user->isDoctor()) {
            $doctor = $user->doctor;
            $stats['my_appointments'] = Appointment::where('doctor_id', $doctor->id)
                ->whereIn('status', ['pending', 'approved'])
                ->count();
            $recentAppointments = Appointment::where('doctor_id', $doctor->id)
                ->with(['patient.user'])
                ->orderBy('appointment_date', 'desc')
                ->take(5)
                ->get();
        } elseif ($user->isPatient()) {
            $patient = $user->patient;
            $stats['my_appointments'] = Appointment::where('patient_id', $patient->id)
                ->whereIn('status', ['pending', 'approved'])
                ->count();
            $recentAppointments = Appointment::where('patient_id', $patient->id)
                ->with(['doctor.user'])
                ->orderBy('appointment_date', 'desc')
                ->take(5)
                ->get();
        } else {
            $recentAppointments = Appointment::with(['patient.user', 'doctor.user'])
                ->orderBy('appointment_date', 'desc')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentAppointments'));
    }
}