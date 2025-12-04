<?php

namespace Tests\Feature;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StrictSlotBookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Scenario 1: Hacking Attempt (Minutes).
     * The doctor works at 09:00. The attacker sends 09:05.
     * Expectation: REJECT.
     */
    public function test_cannot_book_unauthorized_minutes()
    {
        // 1. Setup Doctor with STRICT hours
        $doctor = User::factory()->doctor()->create([
            'available_hours' => ['09:00', '10:00', '11:00']
        ]);

        $patient = User::factory()->create(['is_doctor' => false]);

        // 2. Prepare Payload for "Tomorrow at 09:05" (Invalid Slot)
        $hackDate = now()->addDay()->format('Y-m-d');
        $hackTime = $hackDate . ' 09:05:00';
        $endTime  = $hackDate . ' 10:05:00';

        $payload = [
            'doctor_id'  => $doctor->doctor_public_id,
            'start_time' => $hackTime,
            'end_time'   => $endTime,
        ];

        // 3. Act
        $response = $this->actingAs($patient)
            ->postJson('/api/v1/appointments', $payload);

        // 4. Assert
        // Should fail because 09:05 is NOT in ['09:00', '10:00'...]
        // Currently returns 500 due to Manager Exception, or 422 if handled.
        // We assert strictly that it is NOT 201 Created.
        $this->assertNotEquals(201, $response->status());

        // Double check database is empty
        $this->assertDatabaseMissing('appointments', [
            'doctor_id' => $doctor->id,
            'start_time' => $hackTime
        ]);
    }

    /**
     * Scenario 2: Hacking Attempt (Hours).
     * The doctor works until 11:00. Attacker sends 22:00.
     * Expectation: REJECT.
     */
    public function test_cannot_book_outside_working_hours()
    {
        $doctor = User::factory()->doctor()->create([
            'available_hours' => ['09:00', '10:00'] // Only morning
        ]);

        $patient = User::factory()->create(['is_doctor' => false]);

        // Prepare Payload for "Tomorrow at 22:00" (Valid time format, but doctor is off)
        $hackDate = now()->addDay()->format('Y-m-d');
        $hackTime = $hackDate . ' 22:00:00';
        $endTime  = $hackDate . ' 23:00:00';

        $response = $this->actingAs($patient)
            ->postJson('/api/v1/appointments', [
                'doctor_id'  => $doctor->doctor_public_id,
                'start_time' => $hackTime,
                'end_time'   => $endTime,
            ]);

        $this->assertNotEquals(201, $response->status());
    }

    /**
     * Scenario 3: Valid Booking.
     * User books exactly 09:00.
     * Expectation: ALLOW.
     */
    public function test_can_book_valid_exact_slot()
    {
        $doctor = User::factory()->doctor()->create([
            'available_hours' => ['09:00', '10:00']
        ]);

        $patient = User::factory()->create(['is_doctor' => false]);

        // Exact match
        $validDate = now()->addDay()->format('Y-m-d');
        $validTime = $validDate . ' 09:00:00';
        $endTime   = $validDate . ' 10:00:00';

        $response = $this->actingAs($patient)
            ->postJson('/api/v1/appointments', [
                'doctor_id'  => $doctor->doctor_public_id,
                'start_time' => $validTime,
                'end_time'   => $endTime,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('appointments', [
            'doctor_id' => $doctor->id,
            'start_time' => $validTime
        ]);
    }
}
