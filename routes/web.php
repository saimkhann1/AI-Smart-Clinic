<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientRecordController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PatientAuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

// Protected Routes (Login ke baad hi chalenge)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/register', [PatientAuthController::class, 'showRegisterForm'])->name('patient.register.form');
    Route::post('/register', [PatientAuthController::class, 'register'])->name('patient.register.submit');
    // 1. Dashboard (Main Screen)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Doctor Management
    // Doctors ki list dekhna (Patients ke liye) aur Profile update karna (Doctors ke liye)
    Route::resource('doctors', DoctorController::class)->only(['index', 'show', 'edit', 'update']);

    // 3. Appointment Management
    // Book karna, status change karna, list dekhna
    Route::resource('appointments', AppointmentController::class);

    // 4. Patient Medical History
    Route::get('/my-medical-record', [PatientRecordController::class, 'show'])->name('patient.record.show');
    Route::post('/my-medical-record', [PatientRecordController::class, 'update'])->name('patient.record.update');

    // 5. Invoices (Billing)
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    // Invoices wale section mein ye line add karein
    Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'markAsPaid'])->name('invoices.pay');
    // Profile Settings (Breeze Default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/create-doctor', [AdminController::class, 'createDoctor'])->name('admin.create-doctor');
    Route::post('/admin/create-doctor', [AdminController::class, 'storeDoctor'])->name('admin.store-doctor');

    Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.send');
});

require __DIR__ . '/auth.php';
