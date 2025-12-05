<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateAvailabilityRequest;
use App\Managers\Doctor\DoctorProfileManager;
use App\Http\Resources\User\UserResource;
use Exception as ExceptionAlias;
use Illuminate\Http\JsonResponse;

class DoctorProfileController extends Controller
{
    public function __construct(
        private readonly DoctorProfileManager $manager
    ) {}

    /**
     * @throws ExceptionAlias
     */
    public function update(UpdateAvailabilityRequest $request): JsonResponse
    {
        $doctor = $this->manager->updateProfile(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => new UserResource($doctor)
        ]);
    }
}
