<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_medical_records_table.php

    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            // User ID (Foreign Key) - Taake pata chale ye kis patient ka record hai
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Medical Fields
            $table->string('blood_group')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_diseases')->nullable(); // Purani bimariyan
            $table->text('current_medications')->nullable(); // Abhi jo dawai chal rahi hai

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
