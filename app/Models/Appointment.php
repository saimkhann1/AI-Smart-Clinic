<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'time',
        'status',
        'problem_description',
        'doctor_notes'
    ];

    // Appointment ka Patient
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Appointment ka Doctor
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Appointment ka Invoice
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
