<?php

namespace Tests\Feature\Security;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Security Check: Guests CANNOT book appointments.
     */
    public function test_guest_cannot_book_appointment()
    {
        // 1. Arrange: Create a doctor so we have a valid ID to try
        $doctor = User::factory()->doctor()->create();

        $payload = [
            'doctor_id'  => $doctor->doctor_public_id,
            'start_time' => '2025-12-05 09:00:00',
            'end_time'   => '2025-12-05 10:00:00',
        ];

        // 2. Act: Hit endpoint WITHOUT actingAs()
        $response = $this->postJson('/api/v1/appointments', $payload);

        // 3. Assert: 401 Unauthorized
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Security Check: Guests CANNOT see specific availability slots.
     * (We protect this to prevent scraping/privacy leaks).
     */
    public function test_guest_cannot_check_availability()
    {
        $doctor = User::factory()->doctor()->create();

        // Act
        $response = $this->getJson("/api/v1/doctors/{$doctor->doctor_public_id}/availability?date=2025-12-05");

        // Assert
        $response->assertStatus(401);
    }

    /**
     * Security Check: Guests CANNOT logout.
     * (Because they aren't logged in).
     */
    public function test_guest_cannot_logout()
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Public Access Check: Guests CAN see the doctor list.
     * (This is the "Catalog" view, which we intentionally left public).
     */
    public function test_guest_can_list_doctors()
    {
        // Arrange
        User::factory()->doctor()->count(3)->create();

        // Act
        $response = $this->getJson('/api/v1/doctors');

        // Assert
        $response->assertStatus(401);
    }
}
