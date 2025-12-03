<?php

namespace Tests\Unit\Query;

use App\Models\Appointment\Appointment;
use App\Models\User\User;
use App\Queries\Appointment\GetDoctorAvailabilityQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetDoctorAvailabilityQueryTest extends TestCase
{
    use RefreshDatabase;

    private GetDoctorAvailabilityQuery $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = new GetDoctorAvailabilityQuery();
    }

    public function test_it_returns_true_when_slot_is_free()
    {
        $doctor = User::factory()->doctor()->create();

        // Check a random slot
        $isAvailable = $this->query->isSlotAvailable(
            $doctor->id,
            '2025-01-01 10:00:00',
            '2025-01-01 11:00:00'
        );

        $this->assertTrue($isAvailable);
    }

    public function test_it_returns_false_when_slot_overlaps()
    {
        $doctor = User::factory()->doctor()->create();
        $patient = User::factory()->create();

        // Create an appointment 10:00 - 11:00
        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'start_time' => '2025-01-01 10:00:00',
            'end_time'   => '2025-01-01 11:00:00',
            'status'     => 'scheduled',
            'price'      => 50,
            'is_paid'    => false
        ]);

        // Try to check 10:30 - 11:30 (Overlap!)
        $isAvailable = $this->query->isSlotAvailable(
            $doctor->id,
            '2025-01-01 10:30:00',
            '2025-01-01 11:30:00'
        );

        $this->assertFalse($isAvailable);
    }
}
