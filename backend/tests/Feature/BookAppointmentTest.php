<?php

namespace Tests\Feature;

use App\Models\Appointment\Appointment;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookAppointmentTest extends TestCase
{
    // This trait ensures the DB transaction is rolled back after each test
    // So your test data doesn't clutter your actual database.
    use RefreshDatabase;

    /**
     * Test that a patient can successfully book an available slot.
     */
    public function test_patient_can_book_available_slot()
    {
        // 1. ARRANGE: Create the world state
        // Create a doctor using our specific "doctor" state from the factory
        $doctor = User::factory()->doctor()->create();

        // Create a regular patient
        $patient = User::factory()->create(['is_doctor' => false]);

        // DYNAMIC DATES: Always book for "Tomorrow" at 9 AM
        // This fixes the "start_time must be a date after now" error.
        $startTime = now()->addDay()->setHour(9)->setMinute(0)->setSecond(0);
        $endTime   = $startTime->copy()->addHour();

        // Define the booking data
        $payload = [
            'doctor_id'  => $doctor->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time'   => $endTime->toDateTimeString(),
        ];

        // 2. ACT: Hit the API endpoint acting as the patient
        $response = $this->actingAs($patient)
            ->postJson('/api/v1/appointments', $payload);

        // 3. ASSERT: Verify the result
        // Check HTTP status code 201 (Created)
        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'scheduled');

        // Verify the data actually exists in the PostgreSQL database
        $this->assertDatabaseHas('appointments', [
            'doctor_id'  => $doctor->id,
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

        // DYNAMIC DATES: Always book for "Tomorrow" at 9 AM
        $startTime = now()->addDay()->setHour(9)->setMinute(0)->setSecond(0);
        $endTime   = $startTime->copy()->addHour();

        // Create an EXISTING appointment for Patient 1
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
        // Patient 2 tries to book the EXACT SAME slot
        $payload = [
            'doctor_id'  => $doctor->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time'   => $endTime->toDateTimeString(),
        ];

        $response = $this->actingAs($patient2)
            ->postJson('/api/v1/appointments', $payload);

        // 3. ASSERT
        // Should fail with Server Error (500) because our Manager throws an Exception
        // In a production app, we would catch this and return 422 (Unprocessable Entity)
        $response->assertStatus(500);
    }
}
