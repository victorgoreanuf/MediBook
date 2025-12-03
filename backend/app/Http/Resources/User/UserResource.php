<?php

namespace App\Http\Resources\User;

use App\Models\User\User; // Import the Model you are wrapping
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User // 🚨 FIX: This tells IDEs and tools that $this within the Resource
 * // is an instance of the App\Models\User\User model.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 1. Identify the Viewer
        // If the request is authenticated, get the user.
        // During login/register, the user might not be in $request->user() yet,
        // but since we are returning their OWN data, we treat them as authorized for private data.
        $viewer = $request->user();

        // Check if the viewer is the owner of this resource
        // We assume true for 'auth' routes where we explicitly return the current user.
        $isOwner = $viewer && $viewer->id === $this->id;

        // 2. Base Public Data (Always Visible)
        $data = [
            'name' => $this->name,
        ];

        // 3. Doctor Specific Data (Visible to Everyone if the user is a doctor)
        if ($this->is_doctor) {
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
