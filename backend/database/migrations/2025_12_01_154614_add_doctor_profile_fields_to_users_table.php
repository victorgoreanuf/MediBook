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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('doctor_public_id')->nullable()->unique()->after('id');
            $table->boolean('is_doctor')->default(false);
            $table->string('specialization')->nullable(); // might need to make it in a enum
            $table->text('bio')->nullable();
            $table->json('available_hours')->nullable(); // ["09:00", "10:00", "11:00"]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'doctor_public_id',
                'is_doctor',
                'specialization',
                'bio',
                'available_hours'
            ]);
        });
    }
};
