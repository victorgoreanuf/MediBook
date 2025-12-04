<?php

namespace App\Queries\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;

class GetAllDoctorsQuery
{
    /**
     * Get all users who are flagged as doctors.
     * Use strict typing for the return value.
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return User::query()
            ->where('is_doctor', true)
            ->orderBy('name')
            // Eager load relationships if you add them later (e.g. reviews)
            ->get();
    }
}
