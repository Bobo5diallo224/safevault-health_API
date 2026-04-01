<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->resource['access_token'],
            'token_type'   => $this->resource['token_type'],
            'expires_at'   => $this->resource['expires_at'],
            'user' => [
                'id'    => $this->resource['user']->id,
                'email' => $this->resource['user']->email,
                'role'  => $this->resource['user']->role,
            ],
        ];
    }
}
