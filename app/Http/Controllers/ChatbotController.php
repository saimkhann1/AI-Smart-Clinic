<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Invoice; 
use App\Models\User;
use App\Models\MedicalRecord;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $apiKey = trim(env('GEMINI_API_KEY'));
        
        if (!$apiKey) {
            return response()->json(['reply' => 'Error: API Key missing hai.'], 500);
        }

        // 1. User ki Details (Context Injection)
        $user = Auth::user();
        $userName = $user ? $user->name : 'Guest';
        $userRole = $user ? $user->role : 'guest';

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        // =========================================================
        // 2. ROLE-BASED AI LOGIC (TOOLS & INSTRUCTIONS)
        // =========================================================
        $systemInstruction = "";
        $aiTools = [];

        // 🟢 AGAR USER PATIENT HAI
        if ($userRole === 'patient') {
            $systemInstruction = "Tum 'Smart Clinic' ke AI Agent ho. Is waqt {$userName} (Patient) tumse baat kar raha hai. Tumhara kaam uski appointment book karna hai. Sirf Roman Urdu mein baat karo.";
            $aiTools = [
                [
                    'name' => 'book_appointment',
                    'description' => 'Nayi appointment book karne ke liye.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'doctor_name' => ['type' => 'STRING', 'description' => 'Doctor ka naam'],
                            'appointment_date' => ['type' => 'STRING', 'description' => 'Appointment ki exact date YYYY-MM-DD'],
                            'appointment_time' => ['type' => 'STRING', 'description' => 'Appointment ka waqt'],
                            'problem_description' => ['type' => 'STRING', 'description' => 'Patient ki beemari']
                        ],
                        'required' => ['doctor_name', 'appointment_date', 'appointment_time']
                    ]
                ]
            ];
        } 
        // 🔴 AGAR USER ADMIN YA RECEPTIONIST HAI
        elseif (in_array($userRole, ['admin', 'receptionist'])) {
            $systemInstruction = "Tum 'Smart Clinic' ke Admin Assistant ho. Is waqt {$userName} (Admin) login hai. Tumhara kaam patients ka bill (invoice) banana hai. Tumne fee nahi poochni, sirf patient ka naam confirm karo aur 'generate_invoice' chalao. Sirf Roman Urdu mein baat karo.";
            $aiTools = [
                [
                    'name' => 'generate_invoice',
                    'description' => 'Kisi patient ka bill generate karne ke liye. Amount ya fee nahi mangni.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            // 💥 DEKHEN: Yahan se amount property hata di hai 💥
                            'patient_name' => ['type' => 'STRING', 'description' => 'Patient ka naam jiska bill banana hai']
                        ],
                        'required' => ['patient_name']
                    ]
                ]
            ];
        }
        // 🔵 AGAR USER DOCTOR HAI
        else {
            $systemInstruction = "Tum 'Smart Clinic' ke Doctor's Assistant ho. Is waqt Dr. {$userName} login hain. Tumhara kaam unki aaj ki real appointments batana hai. Agar doctor apni appointments puche to khud se naam mat banana, foran 'get_today_appointments' function call karo. Sirf Roman Urdu mein baat karo.";
            $aiTools = [
                [
                    'name' => 'get_today_appointments',
                    'description' => 'Doctor ki aaj ki appointments database se nikalne ke liye.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'dummy' => ['type' => 'STRING', 'description' => 'Just pass auto']
                        ]
                    ]
                ]
            ];
        }

        // =========================================================
        // AI KO REQUEST BHEJNA
        // =========================================================
        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [['text' => $systemInstruction]]
                    ],
                    'tools' => [
                        ['functionDeclarations' => $aiTools]
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $request->message]]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (!isset($responseData['candidates'][0]['content']['parts'][0])) {
                    return response()->json(['reply' => 'Maaf kijiye, main theek se samajh nahi paya.']);
                }

                $parts = $responseData['candidates'][0]['content']['parts'][0];

                // 💥 CHECK KAREIN KYA AI NE KOI FUNCTION CHALAYA HAI? 💥
                if (isset($parts['functionCall'])) {
                    
                    $functionName = $parts['functionCall']['name'];
                    $args = $parts['functionCall']['args'] ?? [];

                    // ------------------------------------------
                    // ACTION 1: APPOINTMENT BOOKING (For Patient)
                    // ------------------------------------------
                    if ($functionName === 'book_appointment') {
                        $patientId = Auth::id();
                        if (!$patientId) return response()->json(['reply' => '⚠️ Appointment book karne ke liye please login karein.']);

                        $doctorName = $args['doctor_name'];
                        $doctor = User::where('name', 'LIKE', "%{$doctorName}%")->whereIn('role', ['doctor', 'admin'])->first();
                        if (!$doctor) return response()->json(['reply' => "⚠️ 'Dr. {$doctorName}' system mein nahi mile."]);

                        $date = Carbon::parse($args['appointment_date'])->format('Y-m-d');
                        $time = $args['appointment_time'];
                        $problem = $args['problem_description'] ?? 'Booked via AI';

                        $appointment = Appointment::create([
                            'patient_id' => $patientId,
                            'doctor_id' => $doctor->id,
                            'date' => $date,
                            'time' => $time,
                            'status' => 'pending',
                            'problem_description' => $problem
                        ]);

                        MedicalRecord::create([
                            'patient_id' => $patientId,
                            'appointment_id' => $appointment->id,
                            'doctor_id' => $doctor->id,
                            'symptoms' => $problem, 
                            'doctor_notes' => 'Awaiting Checkup', 
                            'prescription' => null
                        ]);

                        return response()->json(['reply' => "✅ Zabardast! Aapki appointment **Dr. {$doctor->name}** ke sath book ho gayi hai aur aapka Medical Record generate kar diya gaya hai."]);
                    }

                    // ------------------------------------------
                    // ACTION 2: INVOICE GENERATION (For Admin) 💰
                    // ------------------------------------------
                    if ($functionName === 'generate_invoice') {
                        $patientName = $args['patient_name'] ?? '';

                        // 1. Patient Dhoondo
                        $patient = User::where('name', 'LIKE', "%{$patientName}%")->first();
                        if (!$patient) {
                            return response()->json(['reply' => "⚠️ Maaf kijiye, '{$patientName}' naam ka koi patient system mein nahi mila."]);
                        }

                        // 2. Patient Ki Appointment Dhoondo
                        $latestAppointment = Appointment::where('patient_id', $patient->id)->latest()->first();
                        if (!$latestAppointment) {
                            return response()->json(['reply' => "⚠️ '{$patient->name}' ki koi appointment nahi mili. Bill banane ke liye pehle appointment zaroori hai."]);
                        }

                        // 3. 💥 AUTO FETCH DOCTOR & FEE 💥
                        $doctor = User::find($latestAppointment->doctor_id);
                        $doctorProfile = $doctor ? $doctor->doctorProfile : null;
                        
                        // Doctor ki fee database se lagao, agar nahi hai to 1000 laga do
                        $amount = ($doctorProfile && $doctorProfile->consultation_fee) ? $doctorProfile->consultation_fee : 1000;

                        $invoiceNumber = 'INV-' . strtoupper(uniqid());

                        // 4. Invoice Create
                        Invoice::create([
                            'appointment_id' => $latestAppointment->id,
                            'patient_id' => $patient->id,
                            'invoice_number' => $invoiceNumber,
                            'amount' => $amount,
                            'payment_status' => 'unpaid'
                        ]);

                        $docName = $doctor ? $doctor->name : 'Doctor';
                        return response()->json(['reply' => "✅ Done! **{$patient->name}** ka bill successfully ban gaya hai. Dr. {$docName} ki fee **Rs. {$amount}** automatically lag gayi hai. (Invoice No: {$invoiceNumber})"]);
                    }

                    // ------------------------------------------
                    // ACTION 3: DOCTOR'S REAL APPOINTMENTS 🩺
                    // ------------------------------------------
                    if ($functionName === 'get_today_appointments') {
                        $doctorId = Auth::id();
                        $today = Carbon::today()->toDateString(); 
                        
                        $appointments = Appointment::where('doctor_id', $doctorId)
                                                   ->where('date', $today)
                                                   ->with('patient')
                                                   ->orderBy('time', 'asc')
                                                   ->get();
                        
                        if ($appointments->isEmpty()) {
                            return response()->json(['reply' => "✅ Dr. {$userName}, database ke mutabiq aaj aapki koi appointment nahi hai. Aap relax kar sakte hain!"]);
                        }

                        $replyText = "✅ Dr. {$userName}, aaj aapki total **" . $appointments->count() . "** appointments hain:\n\n";
                        
                        foreach($appointments as $index => $apt) {
                            $patientName = $apt->patient ? $apt->patient->name : 'Unknown Patient';
                            $formattedTime = Carbon::parse($apt->time)->format('h:i A'); 
                            
                            $replyText .= ($index + 1) . ". Waqt: **{$formattedTime}** | Patient: **{$patientName}** | Masla: {$apt->problem_description}\n";
                        }

                        return response()->json(['reply' => $replyText]);
                    }

                } // END OF FUNCTION CALLS

                // ------------------------------------------
                // NORMAL CHAT REPLY
                // ------------------------------------------
                $reply = $parts['text'] ?? 'Main samajh nahi paya.';
                return response()->json(['reply' => $reply]);

            } else {
                return response()->json(['reply' => 'Google API Error: ' . $response->body()], 500);
            }
            
        // ------------------------------------------
        // ERROR HANDLING (CATCH BLOCK)
        // ------------------------------------------
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}