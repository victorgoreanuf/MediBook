<?php

namespace database\factories;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// Note the namespace change!

class UserFactory extends Factory
{
    // Tell Laravel this factory is for our specific User model
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'is_doctor' => false, // Default is patient
        ];
    }

    // Define a "state" for Doctors
    public function doctor(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_doctor' => true,
            'specialization' => fake()->randomElement(['Cardiologist', 'Dermatologist', 'Pediatrician', 'General Practitioner']),
            'bio' => fake()->paragraph(),
            // Simple JSON schedule: 9 AM to 5 PM
            'available_hours' => ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
        ]);
    }
}
