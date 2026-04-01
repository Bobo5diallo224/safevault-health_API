<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\RegisterRequest;

final readonly class RegisterDTO
{
    public function __construct(
        public string  $email,
        public string  $password,
        public string  $role,
        public ?string $firstName = null,
        public ?string $lastName  = null,
        public ?string $birthDate = null,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            email:     $request->validated('email'),
            password:  $request->validated('password'),
            role:      $request->validated('role'),
            firstName: $request->validated('first_name'),
            lastName:  $request->validated('last_name'),
            birthDate: $request->validated('birth_date'),
        );
    }
}