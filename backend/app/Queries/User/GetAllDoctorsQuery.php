<?php

namespace App\Queries\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;

class GetAllDoctorsQuery
{
    public function get(): Collection
    {
        return User::query()
            ->where('is_doctor', true)
            ->orderBy('name')
            // Eager load relationships if you add them later (e.g. reviews)
            ->get();
    }
}
