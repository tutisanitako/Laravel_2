<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['visit.appointment.patient.user', 'visit.appointment.doctor.user']);

        $user = auth()->user();

        // Role-based filtering
        if ($user->isPatient()) {
            $query->whereHas('visit.appointment', function ($q) use ($user) {
                $q->where('patient_id', $user->patient->id);
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method != '') {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isReceptionist()) {
            abort(403);
        }

        $visitId = $request->query('visit_id');
        $visit = null;

        if ($visitId) {
            $visit = Visit::with(['appointment.patient.user'])->findOrFail($visitId);
            
            // Check if visit already has a payment
            if ($visit->payment) {
                return redirect()->route('payments.show', $visit->payment->id)
                    ->with('error', 'This visit already has a payment record.');
            }
        }

        $visits = Visit::with(['appointment.patient.user', 'appointment.doctor.user'])
            ->whereDoesntHave('payment')
            ->get();

        return view('payments.create', compact('visits', 'visit'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isReceptionist()) {
            abort(403);
        }

        $validated = $request->validate([
            'visit_id' => ['required', 'exists:visits,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,insurance'],
            'status' => ['required', 'in:paid,unpaid'],
        ]);

        $visit = Visit::findOrFail($validated['visit_id']);

        // Check if visit already has a payment
        if ($visit->payment) {
            return back()->with('error', 'This visit already has a payment record.');
        }

        $payment = Payment::create($validated);

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment created successfully.');
    }

    public function show(Payment $payment)
    {
        $user = auth()->user();

        // Authorization check
        if ($user->isPatient() && $payment->visit->appointment->patient_id !== $user->patient->id) {
            abort(403);
        }

        $payment->load(['visit.appointment.patient.user', 'visit.appointment.doctor.user']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isReceptionist()) {
            abort(403);
        }

        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isReceptionist()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,insurance'],
            'status' => ['required', 'in:paid,unpaid'],
        ]);

        $payment->update($validated);

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    public function markAsPaid(Payment $payment)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isReceptionist()) {
            abort(403);
        }

        $payment->update(['status' => 'paid']);
        return back()->with('success', 'Payment marked as paid successfully.');
    }
}