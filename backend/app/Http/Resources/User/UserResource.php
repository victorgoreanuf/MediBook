<?php

namespace App\Http\Resources\User;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $viewer = $request->user();
        $isOwner = $viewer && $viewer->id === $this->id;

        $data = [
            'name' => $this->name,
        ];

        if ($this->is_doctor) {
            $data['is_doctor'] = $this->is_doctor;
            $data['id'] = $this->doctor_public_id;
            $data['specialization'] = $this->specialization;
            $data['bio'] = $this->bio;
            $data['available_hours'] = $this->available_hours;
        }

        if ($isOwner) {
            $data['email'] = $this->email;
        }

        return $data;
    }
}
