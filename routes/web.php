<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Patient Routes - Admin and Receptionist can manage all
    Route::middleware(['role:admin,receptionist'])->group(function () {
        Route::resource('patients', PatientController::class)->except(['show']);
    });
    
    // All authenticated users can view patient details
    Route::get('patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

    // Doctor Routes - Admin can manage all
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('doctors', DoctorController::class)->except(['show']);
    });
    
    // All authenticated users can view doctor details
    Route::get('doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');

    // Appointment Routes
    Route::resource('appointments', AppointmentController::class);
    Route::post('appointments/{appointment}/approve', [AppointmentController::class, 'approve'])
        ->name('appointments.approve')
        ->middleware('role:admin,receptionist,doctor');
    Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->name('appointments.cancel');
    Route::post('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
        ->name('appointments.complete')
        ->middleware('role:doctor');

    // Visit Routes - Only doctors can create/edit, others can view
    Route::get('visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
    
    Route::middleware(['role:doctor'])->group(function () {
        Route::get('visits/create', [VisitController::class, 'create'])->name('visits.create');
        Route::post('visits', [VisitController::class, 'store'])->name('visits.store');
        Route::get('visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');
        Route::put('visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
    });
    
    Route::delete('visits/{visit}', [VisitController::class, 'destroy'])
        ->name('visits.destroy')
        ->middleware('role:admin');

    // Payment Routes
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    
    Route::middleware(['role:admin,receptionist'])->group(function () {
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::post('payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-paid');
    });
    
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])
        ->name('payments.destroy')
        ->middleware('role:admin');
});

require __DIR__.'/auth.php';