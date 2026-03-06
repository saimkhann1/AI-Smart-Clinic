<?php

// app/Http/Controllers/AppointmentController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // API call ke liye
use Illuminate\Support\Str;
class AppointmentController extends Controller
{
    // List all appointments
    public function index()
    {
        // Dashboard handle kar raha hai, lekin alag page ke liye bhi rakh sakte hain
        return redirect()->route('dashboard');
    }

    // Show Booking Form
    public function create()
    {
        // Sirf un users ko bhejo jo role 'doctor' rakhte hain aur unki profile bani hui hai
        $doctors = User::where('role', 'doctor')->with('doctorProfile')->get();

        return Inertia::render('Appointments/Create', [
            'doctors' => $doctors
        ]);
    }

    // Store Appointment (Booking Logic)
    public function store(Request $request)
    {
        // 1. Validation (Ab hum Medical fields bhi validate karenge)
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'problem' => 'required|string|max:500',
            // Medical Record Fields
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'current_medications' => 'nullable|string',
        ]);

        $user = auth()->user();

        // 2. Appointment Create Karna
        \App\Models\Appointment::create([
            'patient_id' => $user->id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
            'problem_description' => $request->problem,
        ]);

        // 3. Medical Record Update ya Create Karna (Magic Step)
        // Ye check karega agar record hai to update kardo, nahi to naya bana do
        $user->medicalRecord()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'blood_group' => $request->blood_group,
                'allergies' => $request->allergies,
                'chronic_diseases' => $request->chronic_diseases,
                'current_medications' => $request->current_medications,
            ]
        );

        return redirect()->route('dashboard')->with('message', 'Appointment Booked Successfully!');
    }

    // Update Status (Doctor ke liye - Cancel/Confirm/Complete)
   public function update(Request $request, string $id)
{
    // 1. Appointment dhoondo
    $appointment = Appointment::findOrFail($id);

    // 2. Validation (Sirf status update allowed hai)
    $request->validate([
        'status' => 'required|in:pending,confirmed,completed,cancelled',
    ]);

    // 3. Status Update karo
    $appointment->status = $request->status;
    $appointment->save();

    // --- MAIN LOGIC: AUTO-GENERATE INVOICE ---
    // Agar status 'completed' hai AUR iski invoice pehle se nahi bani hui
    if ($appointment->status === 'completed' && !$appointment->invoice) {
        
        $doctorFee = $appointment->doctor->doctorProfile->consultation_fee ?? 1000;

        Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'appointment_id' => $appointment->id,
            
            // CORRECTED: 'user_id' ko hata kar 'patient_id' kar diya
            'patient_id' => $appointment->patient_id, 
            
            'amount' => $doctorFee,
            'payment_status' => 'unpaid',
        ]);
    }

    return redirect()->back()->with('message', 'Status updated & Invoice generated (if completed)!');
}
}
