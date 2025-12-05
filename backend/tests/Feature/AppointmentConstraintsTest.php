<?php

namespace Tests\Feature;

use App\Models\Appointment\Appointment;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentConstraintsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Rule: User cannot book the SAME doctor twice in one day.
     */
    public function test_patient_cannot_book_same_doctor_twice_in_one_day()
    {
        // Arrange
        $doctor = User::factory()->doctor()->create([
            'available_hours' => ['09:00', '14:00']
        ]);
        $patient = User::factory()->create(['is_doctor' => false]);

        $date = now()->addDay()->format('Y-m-d');

        // 1. Create First Appointment (Morning) - Should Succeed
        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'start_time' => "$date 09:00:00",
            'end_time'   => "$date 10:00:00",
            'status'     => 'scheduled',
            'price'      => 100
        ]);

        // 2. Try to book Second Appointment (Afternoon) - Should Fail
        $response = $this->actingAs($patient)->postJson('/api/v1/appointments', [
            'doctor_id' => $doctor->doctor_public_id,
            'start_time' => "$date 14:00:00",
            'end_time'   => "$date 15:00:00"
        ]);

        // Assert
        $response->assertStatus(500); // Or 422 if you handle exceptions
        // We can assert the error message if we want to be specific, but status code is enough for feature test
    }

    /**
     * Rule: User cannot be in two places at once.
     */
    public function test_patient_cannot_have_simultaneous_appointments_with_different_doctors()
    {
        // Arrange
        $doctorA = User::factory()->doctor()->create(['available_hours' => ['10:00']]);
        $doctorB = User::factory()->doctor()->create(['available_hours' => ['10:00']]);
        $patient = User::factory()->create(['is_doctor' => false]);

        $date = now()->addDay()->format('Y-m-d');

        // 1. Book Doctor A at 10:00
        Appointment::create([
            'doctor_id' => $doctorA->id,
            'patient_id' => $patient->id,
            'start_time' => "$date 10:00:00",
            'end_time'   => "$date 11:00:00",
            'status'     => 'scheduled',
            'price'      => 100
        ]);

        // 2. Try to book Doctor B at the SAME time (10:00)
        $response = $this->actingAs($patient)->postJson('/api/v1/appointments', [
            'doctor_id' => $doctorB->doctor_public_id,
            'start_time' => "$date 10:00:00",
            'end_time'   => "$date 11:00:00"
        ]);

        // Assert
        $response->assertStatus(500);
        $this->assertDatabaseMissing('appointments', ['doctor_id' => $doctorB->id]);
    }
}
