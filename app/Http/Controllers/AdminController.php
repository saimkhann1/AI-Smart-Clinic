<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    // Admin Dashboard Main Page
    // Admin Dashboard Main Page
    public function index()
    {
        // 1. Statistics (Stats)
        $stats = [
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_appointments' => Appointment::count(),
            'total_revenue' => Invoice::where('payment_status', 'paid')->sum('amount'),
        ];

        // 2. Recent Appointments
        $recent_appointments = Appointment::with(['doctor', 'patient'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 3. Doctors List (YE CHEEZ ADD KARNI HAI)
        // Hum doctors ko unki profile ke sath le rahe hain taake fee aur specialization dikha sakein
        $doctors = User::where('role', 'doctor')->with('doctorProfile')->get();

        // 4. Return to View with Data
        return Inertia::render('Admin/dashboard', [
            'stats' => $stats,
            'recent_appointments' => $recent_appointments,
            'doctors' => $doctors // <--- Ye zaroori hai table show karne ke liye
        ]);
    }

    // Naya Doctor Banane ka Form
    public function createDoctor()
    {
        return Inertia::render('Admin/CreateDoctor');
    }

    // Doctor ko Database mein Save karna
    // App/Http/Controllers/AdminController.php

    public function storeDoctor(Request $request)
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'specialization' => 'required|string|max:255',
            'consultation_fee' => 'required|numeric',
        ]);

        // 2. User Create karein
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
            'phone' => $request->phone ?? null,
        ]);

        // 3. Profile Create karein
        DoctorProfile::create([
            'user_id' => $user->id,
            'specialization' => $request->specialization,
            'consultation_fee' => $request->consultation_fee,
            'available_days' => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            'start_time' => '09:00',
            'end_time' => '17:00'
        ]);

        // CORRECTION HERE:
        // Route ka naam wahi likhein jo web.php mein 'name()' ke andar hai.
        return redirect()->route('Admin.dashboard')->with('message', 'New Doctor Added Successfully!');
    }
}
