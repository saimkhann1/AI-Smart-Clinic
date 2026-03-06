<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DoctorProfile;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@clinic.com',
            'password' => bcrypt('password'), // Password 'password' hoga
            'role' => 'admin', // Main cheez ye hai
            'phone' => '0000000000',
        ]);
        // 1. Ek Doctor User Create karein
        $doctor = User::create([
            'name' => ' Baqer',
            'email' => 'doctor@clinic.com',
            'role' => 'doctor', // Ye zaroori hai
            'password' => bcrypt('password'), // Password 'password' hoga
            'phone' => '1234567890',
        ]);

        // 2. Us Doctor ki Profile bhi banani padegi (Warna error aa sakta hai)
        DoctorProfile::create([
            'user_id' => $doctor->id,
            'specialization' => 'General Physician',
            'qualification' => 'MBBS',
            'consultation_fee' => 1500,
            'available_days' => ['Monday','Tuesday','Wednesday','Thursday','Friday'],
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);

        // Optional: Ek Patient bhi bana lein testing ke liye
        User::create([
            'name' => 'Test Patient',
            'email' => 'patient@clinic.com',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);
    }
}
