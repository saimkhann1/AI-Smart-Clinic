<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'available_days' => 'array', // JSON ko Array mein convert karega automatically
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
