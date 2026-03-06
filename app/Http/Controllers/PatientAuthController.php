<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PatientAuthController extends Controller
{
    // Registration Page Dikhane ke liye
    public function showRegisterForm()
    {
        return Inertia::render('Auth/PatientRegister');
    }

    // Data Save karne aur Login karwane ke liye
    public function register(Request $request)
    {
        // 1. Validation check karein
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'gender' => 'required|string|in:male,female,other',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Naya User Database mein save karein
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'role' => 'patient', // 💥 MAGIC: Yahan hum explicitly role 'patient' set kar rahe hain
            'password' => Hash::make($request->password),
        ]);

        // 3. Fauran Login karwa dein
        Auth::login($user);

        // 4. Patient ke Dashboard par bhej dein
        return redirect()->route('dashboard'); 
    }
}