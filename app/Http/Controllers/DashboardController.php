<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // 💥 Yahan hum strictly check kar rahe hain ke database mein patient_id logged-in user ki ID ke barabar ho
        if ($user->role === 'patient') {
            $myAppointments = Appointment::where('patient_id', $user->id)
                ->with('doctor')
                ->orderBy('date', 'desc')
                ->get();

            return Inertia::render('Patient/Dashboard', [
                'appointments' => $myAppointments
            ]);
        }
    }
    public function index()
    {
        $user = Auth::user();

        // --- ADMIN CHECK ---
        // Agar Admin hai to usay Admin Dashboard par bhej do
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // --- DOCTOR CHECK ---
        // Agar Doctor hai, to uski appointments dikhao
        if ($user->role === 'doctor') {
            $appointments = $user->appointmentsAsDoctor()
                // FIX 1: 'patient' aur 'doctor' dono relations bhej diye taake frontend crash na ho
                ->with(['patient', 'doctor'])
                // FIX 2: Date filter temporary hata diya hai taake purani/kal ki appointments bhi show hon
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->get();

            return Inertia::render('Dashboard', [
                'appointments' => $appointments,
                'user' => $user
            ]);
        }

        // --- PATIENT CHECK (DEFAULT) ---
        // Agar Patient hai, to uski booked appointments dikhao
        else {
            $appointments = $user->appointmentsAsPatient()
                ->with('doctor')
                ->orderBy('date', 'desc')
                ->get();

            $medical_record = $user->medicalRecord;

            return Inertia::render('Dashboard', [
                'appointments' => $appointments,
                'user' => $user,
                'medical_record' => $medical_record
            ]);
        }
    }
}
