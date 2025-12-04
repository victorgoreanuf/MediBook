<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Queries\User\GetAllDoctorsQuery;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DoctorController extends Controller
{
    public function __construct(
        private readonly GetAllDoctorsQuery $doctorsQuery
    ) {}

    /**
     * Get a list of all doctors.
     * * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $doctors = $this->doctorsQuery->get();

        return UserResource::collection($doctors);
    }
}
