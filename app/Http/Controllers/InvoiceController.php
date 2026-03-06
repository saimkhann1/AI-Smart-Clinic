<?php
// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        // Patient apne bills dekhega
        $user = Auth::user();

        $invoices = Invoice::whereHas('appointment', function ($q) use ($user) {
            $q->where('patient_id', $user->id);
        })->with('appointment.doctor')->get();

        return Inertia::render('Invoices/Index', ['invoices' => $invoices]);
    }

    public function show($id)
    {
        // Invoice dhoondo, sath mein Appointment, Doctor, aur Patient ka data bhi load karo
        $invoice = Invoice::with(['appointment.doctor.doctorProfile', 'appointment.patient'])
            ->findOrFail($id);

        // Ye data 'Invoices/Show.vue' file ko bhej do
        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice
        ]);
    }
    // Is function ko InvoiceController class ke andar add karein
    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Status update karein
        $invoice->update([
            'payment_status' => 'paid',
            'payment_method' => 'online_card' // Fake payment method
        ]);

        return redirect()->back()->with('message', 'Payment Successful! Invoice is now Paid.');
    }
}
