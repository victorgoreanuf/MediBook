<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 Doctors
        User::factory()
            ->count(10)
            ->doctor() // Use the state we just defined
            ->create();

        // Create 1 specific doctor for manual testing (so you know the ID)
        User::factory()->doctor()->create([
            'name' => 'Dr. House',
            'email' => 'house@medibook.com',
            'specialization' => 'Diagnostician',
            'id' => 999
        ]);
    }
}
