<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function visit()
    {
        return $this->hasOne(Visit::class);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        if ($doctorId) {
            return $query->where('doctor_id', $doctorId);
        }
        return $query;
    }

    public function scopeByPatient($query, $patientId)
    {
        if ($patientId) {
            return $query->where('patient_id', $patientId);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('appointment_date', $date);
        }
        return $query;
    }
}