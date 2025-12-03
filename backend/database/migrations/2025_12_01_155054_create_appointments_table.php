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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Relationships
            // 'cascade' delete means if the user is deleted, their appointments vanish too
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            // Scheduling Data
            $table->timestamp('start_time');
            $table->timestamp('end_time');

            // Status Management
            // We use string for now, but strict Enums in code are preferred by the Bible [cite: 911]
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled

            // Payment/Business Logic Fields
            $table->decimal('price', 8, 2)->nullable();
            $table->boolean('is_paid')->default(false);

            // Standard Laravel timestamps (created_at, updated_at)
            $table->timestamps();
            // Indexes for performance (very important for the Query layer later!)
            $table->index(['doctor_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
