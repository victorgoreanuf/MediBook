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

        // 2. Base Public Data (Always Visible)
        $data = [
            'name' => $this->name,
        ];
        // 3. Doctor Specific Data (Visible to Everyone if the user is a doctor)
        if ($this->is_doctor) {
            $data['id'] = $this->doctor_public_id;
            $data['specialization'] = $this->specialization;
            $data['bio'] = $this->bio;
            $data['available_hours'] = $this->available_hours;
        }

        // 4. Private Data (Only Visible to Owner)
        // We use $this->when() to conditionally add fields to the JSON response
        if ($isOwner) {
            $data['email'] = $this->email;
        }

        return $data;
    }
}
