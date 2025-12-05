<?php

namespace App\Managers\Doctor;

use App\Models\User\User;
use Exception as ExceptionAlias;
use Illuminate\Support\Facades\DB;

class DoctorProfileManager
{
    /**
     * @throws ExceptionAlias
     */
    public function updateProfile(User $doctor, array $data): User
    {
        DB::beginTransaction();
        try {
            $doctor->update([
                'available_hours' => $data['available_hours'],
                'bio' => $data['bio'] ?? $doctor->bio,
                'specialization' => $data['specialization'] ?? $doctor->specialization,
            ]);

            DB::commit();
            return $doctor;
        } catch (ExceptionAlias $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
