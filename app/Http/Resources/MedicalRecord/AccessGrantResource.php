<?php

namespace App\Http\Resources\MedicalRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessGrantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'is_active'  => $this->is_active,
            'is_valid'   => $this->isValid(),
            'expires_at' => $this->expires_at?->toISOString(),
            'expires_soon' => $this->expiresSoon(),
            'granted_at' => $this->created_at->toISOString(),

            'doctor' => $this->when($this->relationLoaded('doctor'), [
                'id'    => $this->doctor?->id,
                'email' => $this->doctor?->email,
            ]),
        ];
    }
}