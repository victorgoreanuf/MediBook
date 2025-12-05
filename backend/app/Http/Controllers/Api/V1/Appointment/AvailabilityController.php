<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\AvailabilityDataRequest;
use App\DTOs\Appointment\GetAvailabilityDTO;
use App\Services\Appointment\AvailabilityService;
use App\Http\Resources\Appointment\AvailabilitySlotResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AvailabilityController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $service
    ) {}

    public function __invoke(AvailabilityDataRequest $request, string $doctorPublicId): AnonymousResourceCollection
    {
        $dto = GetAvailabilityDTO::fromRequest(
            $doctorPublicId,
            $request->validated()
        );

        $slots = $this->service->getDailySlots($dto);

        return AvailabilitySlotResource::collection(collect($slots));
    }
}
