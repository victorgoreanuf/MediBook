<?php

namespace Tests\Feature;

use App\Models\Appointment\Appointment;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookAppointmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a patient can successfully book an available slot using the Doctor's Public ID.
     */
    public function test_patient_can_book_available_slot()
    {
        // 1. ARRANGE
        // The User model's boot() method will automatically generate a 'doctor_public_id' UUID
        $doctor = User::factory()->doctor()->create();
        $patient = User::factory()->create(['is_doctor' => false]);

        // DYNAMIC DATES: Always book for "Tomorrow" at 9 AM
        $startTime = now()->addDay()->setHour(9)->setMinute(0)->setSecond(0);
        $endTime   = $startTime->copy()->addHour();

        // Define the booking data
        $payload = [
            // 🚨 CRITICAL CHANGE: Send the Public UUID, not the internal ID
            'doctor_id'  => $doctor->doctor_public_id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time'   => $endTime->toDateTimeString(),
        ];

        // 2. ACT
        $response = $this->actingAs($patient)
            ->postJson('/api/v1/appointments', $payload);

        // 3. ASSERT
        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'scheduled');

        // Verify the data exists in DB using the Internal IDs (because the Mapper converted it)
        $this->assertDatabaseHas('appointments', [
            'doctor_id'  => $doctor->id, // Database still stores Integer ID
            'patient_id' => $patient->id,
            'start_time' => $startTime->toDateTimeString(),
            'status'     => 'scheduled'
        ]);
    }

    /**
     * Test that the system prevents double booking.
     */
    public function test_cannot_book_overlapping_slot()
    {
        // 1. ARRANGE
        $doctor = User::factory()->doctor()->create();
        $patient1 = User::factory()->create();
        $patient2 = User::factory()->create();

        $startTime = now()->addDay()->setHour(9)->setMinute(0)->setSecond(0);
        $endTime   = $startTime->copy()->addHour();

        // Create an EXISTING appointment (stored with internal IDs)
        Appointment::create([
            'doctor_id'  => $doctor->id,
            'patient_id' => $patient1->id,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'status'     => 'scheduled',
            'price'      => 100.00,
            'is_paid'    => false
        ]);

        // 2. ACT
        // Patient 2 tries to book the SAME slot using the Public UUID
        $payload = [
            'doctor_id'  => $doctor->doctor_public_id, // 🚨 Use UUID
            'start_time' => $startTime->toDateTimeString(),
            'end_time'   => $endTime->toDateTimeString(),
        ];

        $response = $this->actingAs($patient2)
            ->postJson('/api/v1/appointments', $payload);

        // 3. ASSERT
        // Expect 500 (Manager Exception) or 422
        $response->assertStatus(500);
    }
}
