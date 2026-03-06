<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Invoice extends Model
{
    use HasFactory;

    // 'patient_id' add karein
    protected $fillable = [
        'invoice_number', 
        'appointment_id', 
        'patient_id', 
        'amount', 
        'payment_status', 
        'payment_method'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    
    // Patient relationship (Optional lekin achi practice hai)
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}