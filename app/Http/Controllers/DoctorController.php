<?php
// app/Http/Controllers/DoctorController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    // Patients ke liye: Doctors ki list
    public function index()
    {
        $doctors = User::where('role', 'doctor')->with('doctorProfile')->get();
        return Inertia::render('Doctors/Index', ['doctors' => $doctors]);
    }

    // Doctor ke liye: Apni Profile Edit karna
    public function edit($id)
    {
        // Security check
        if (Auth::id() != $id || Auth::user()->role !== 'doctor') {
            abort(403);
        }

        $profile = DoctorProfile::firstOrCreate(
            ['user_id' => $id],
            ['specialization' => 'General', 'consultation_fee' => 0, 'available_days' => [], 'start_time' => '09:00', 'end_time' => '17:00']
        );

        return Inertia::render('Doctors/Edit', ['profile' => $profile]);
    }

    // Update Logic
    public function update(Request $request, $id)
    {
        $profile = DoctorProfile::where('user_id', $id)->firstOrFail();

        $data = $request->validate([
            'specialization' => 'required|string',
            'consultation_fee' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required',
            'available_days' => 'array'
        ]);

        $profile->update($data);

        return back()->with('message', 'Profile updated successfully.');
    }
}