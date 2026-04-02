<?php

namespace App\Http\Resources\MedicalRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'created_at' => $this->created_at->toISOString(),

            'diagnosis' => $this->when(
                $user->isDoctor() || $user->isAdmin() || $this->isOwnedBy($user),
                $this->diagnosis
            ),
            'treatment' => $this->when(
                $user->isDoctor() || $user->isAdmin() || $this->isOwnedBy($user),
                $this->treatment
            ),

            'patient' => $this->when($this->relationLoaded('patient'), [
                'id'         => $this->patient?->id,
                'first_name' => $this->when(
                    $user->isAdmin() || $this->isOwnedBy($user) || $user->isDoctor(),
                    $this->patient?->first_name
                ),
                'last_name'  => $this->when(
                    $user->isAdmin() || $this->isOwnedBy($user) || $user->isDoctor(),
                    $this->patient?->last_name
                ),
            ]),

            'creator' => $this->when($this->relationLoaded('creator'), [
                'id'    => $this->creator?->id,
                'email' => $this->creator?->email,
                'role'  => $this->creator?->role,
            ]),

            'access_grants' => $this->when(
                $user->isAdmin() || $this->isOwnedBy($user),
                AccessGrantResource::collection($this->whenLoaded('accessGrants'))
            ),
        ];
    }

    private function isOwnedBy($user): bool
    {
        return $this->patient?->user_id === $user->id;
    }
}