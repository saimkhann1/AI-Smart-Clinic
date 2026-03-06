<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Validation & API Key Check
        $request->validate(['message' => 'required|string|max:1000']);
        $apiKey = trim(env('GEMINI_API_KEY'));
        
        if (!$apiKey) {
            return response()->json(['reply' => 'Error: API Key missing hai.']);
        }

        // 2. Current User ki details (Context Injection)
        $user = Auth::user();
        $userName = $user ? $user->name : 'Guest';
        $userRole = $user ? $user->role : 'guest';

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => "Tum 'Smart Clinic' ke ek smart AI Agent ho. Is waqt jo user tumse baat kar raha hai uska naam '{$userName}' hai aur uska role '{$userRole}' hai.
                            
                            BOHOT ZAROORI STRICT RULES: 
                            1. Tumhara kaam patients ki madad karna aur appointments book karna hai.
                            2. Agar koi appointment book karne ko kahe, to lazmi 'book_appointment' function call karo.
                            3. Hamesha sirf Roman Urdu (English alphabets) mein jawab do. Asli Hindi ya Urdu font bilkul use mat karna."]
                        ]
                    ],
                    'tools' => [
                        [
                            'functionDeclarations' => [
                                // SIRF APPOINTMENT BOOKING KA TOOL
                                [
                                    'name' => 'book_appointment',
                                    'description' => 'Ye function tab chalana hai jab patient nayi appointment book karna chahe.',
                                    'parameters' => [
                                        'type' => 'OBJECT',
                                        'properties' => [
                                            'doctor_name' => [
                                                'type' => 'STRING', 
                                                'description' => 'Doctor ka naam (jaise: Ali, Ahmed)'
                                            ],
                                            'appointment_date' => [
                                                'type' => 'STRING', 
                                                'description' => 'Appointment ki exact date YYYY-MM-DD format mein'
                                            ],
                                            'appointment_time' => [
                                                'type' => 'STRING', 
                                                'description' => 'Appointment ka waqt (jaise: 5:00 PM)'
                                            ],
                                            'problem_description' => [
                                                'type' => 'STRING', 
                                                'description' => 'Patient ki beemari ya masla'
                                            ]
                                        ],
                                        'required' => ['doctor_name', 'appointment_date', 'appointment_time']
                                    ]
                                ]
                            ]
                        ]
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
                
                // Agar AI ne koi text nahi diya
                if (!isset($responseData['candidates'][0]['content']['parts'][0])) {
                    return response()->json(['reply' => 'Maaf kijiye, main theek se samajh nahi paya.']);
                }

                $parts = $responseData['candidates'][0]['content']['parts'][0];

                // 3. FUNCTION CALLING CHECK
                if (isset($parts['functionCall'])) {
                    
                    $functionName = $parts['functionCall']['name'];
                    $args = $parts['functionCall']['args'] ?? [];

                    // APPOINTMENT BOOKING LOGIC
                    if ($functionName === 'book_appointment') {
                        $patientId = Auth::id();
                        if (!$patientId) {
                            return response()->json(['reply' => '⚠️ Appointment book karne ke liye please login karein.']);
                        }

                        $doctorName = $args['doctor_name'];
                        // Sirf un users ko dhoondo jinka role doctor ya admin hai
                        $doctor = User::where('name', 'LIKE', "%{$doctorName}%")->whereIn('role', ['doctor', 'admin'])->first();
                        
                        if (!$doctor) {
                            return response()->json(['reply' => "⚠️ Maaf kijiye, 'Dr. {$doctorName}' naam ke koi doctor system mein nahi mile. Please sahi naam batayen."]);
                        }

                        $date = Carbon::parse($args['appointment_date'])->format('Y-m-d');
                        $time = $args['appointment_time'];
                        $problem = $args['problem_description'] ?? 'Booked via AI Assistant';

                        // Database mein data insert karein
                        Appointment::create([
                            'patient_id' => $patientId,
                            'doctor_id' => $doctor->id,
                            'date' => $date,
                            'time' => $time,
                            'status' => 'pending',
                            'problem_description' => $problem
                        ]);

                        return response()->json([
                            'reply' => "✅ Zabardast! Aapki appointment **Dr. {$doctor->name}** ke sath **{$date}** ko **{$time}** baje system mein successfully book ho gayi hai."
                        ]);
                    }
                }

                // 4. NORMAL CHAT LOGIC
                $reply = $parts['text'] ?? 'Main samajh nahi paya.';
                return response()->json(['reply' => $reply]);

            } else {
                return response()->json(['reply' => 'Google API Error: ' . $response->body()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}