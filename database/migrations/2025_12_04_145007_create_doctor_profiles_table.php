<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to Users table
            $table->string('specialization'); // e.g., Cardiologist, Dentist
            $table->string('qualification')->nullable(); // e.g., MBBS, FCPS
            $table->integer('experience_years')->default(0);
            $table->decimal('consultation_fee', 8, 2); // Invoice generation ke liye 
            $table->json('available_days'); // e.g., ["Monday", "Wednesday", "Friday"] - Doctor schedule [cite: 110]
            $table->time('start_time'); // Shift start
            $table->time('end_time');   // Shift end
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
