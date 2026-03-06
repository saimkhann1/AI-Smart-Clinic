<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    // Ye wo columns hain jo hum form se save kar sakte hain
    protected $fillable = [
        'user_id',
        'blood_group',
        'allergies',
        'chronic_diseases',
        'current_medications',
    ];

    // Relationship: Har Medical Record kisi na kisi User (Patient) ka hota hai
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}