<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalRecord;

class PatientRecordController extends Controller
{
    // Page Dikhana (Data Load karna)
   // Page Dikhana (Data Load karna)
    public function show()
    {
        $user = Auth::user();
        
        // 1. User ki Permanent Medical Profile (Blood group, allergies etc.)
        $record = $user->medicalRecord; 

        // 2. 💥 MISSING DATA: User ki saari appointments nikalo 💥
        // with('doctor') isliye lagaya taake frontend par doctor ka naam bhi show ho sake
        $appointments = $user->appointmentsAsPatient()
                             ->with('doctor') 
                             ->orderBy('date', 'desc')
                             ->orderBy('time', 'desc')
                             ->get();

        // 3. Vue file ko dono cheezein bhej do
        return Inertia::render('Patient/MedicalRecord', [
            'record' => $record,
            'appointments' => $appointments // Ab frontend ko appointments mil jayengi!
        ]);
    }

    // Data Save ya Update karna
    public function update(Request $request)
    {
        // 1. Validation
        $data = $request->validate([
            'blood_group' => 'nullable|string|max:3',
            'allergies' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'current_medications' => 'nullable|string',
        ]);

        $user = Auth::user();

        // 2. Magic Method: updateOrCreate
        // Ye check karega: Agar is user ka record hai -> Update karo. Agar nahi hai -> Naya create karo.
        $user->medicalRecord()->updateOrCreate(
            ['user_id' => $user->id], // Search Condition
            $data // New Data to Save
        );

        return redirect()->back()->with('message', 'Medical Record Updated Successfully!');
    }
}