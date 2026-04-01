<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\PatientProfile;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\LoginDTO;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
    
    public function register(RegisterDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'email'    => $dto->email,
                'password' => $dto->password,
                'role'     => $dto->role,
            ]);

            $user->assignRole($dto->role);

            if ($dto->role === 'patient') {
                PatientProfile::create([
                    'user_id'    => $user->id,
                    'first_name' => $dto->firstName ?? '',
                    'last_name'  => $dto->lastName  ?? '',
                    'birth_date' => $dto->birthDate  ?? '',
                ]);
            }

            $token = $user->createToken(
                name: 'auth-token',
                abilities: $this->abilitiesForRole($dto->role),
                expiresAt: now()->addDay()
            );

            return [
                'user'         => $user,
                'access_token' => $token->plainTextToken,
                'token_type'   => 'Bearer',
                'expires_at'   => now()->addDay()->toISOString(),
            ];
        });
    }

    public function login(LoginDTO $dto): array
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new AuthenticationException('Identifiants invalides.');
        }

        if ($user->trashed()) {
            throw new AuthenticationException('Ce compte a été désactivé.');
        }

        $user->tokens()->delete();

        $token = $user->createToken(
            name: 'auth-token',
            abilities: $this->abilitiesForRole($user->role),
            expiresAt: now()->addDay()
        );

        return [
            'user'         => $user,
            'access_token' => $token->plainTextToken,
            'token_type'   => 'Bearer',
            'expires_at'   => now()->addDay()->toISOString(),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    private function abilitiesForRole(string $role): array
    {
        return match ($role) {
            'patient' => [
                'records:read-own',
                'records:share',
                'profile:read',
                'profile:update',
            ],
            'doctor'  => [
                'records:read-granted',
                'records:create',
                'records:update',
            ],
            'admin'   => ['*'],
            default   => [],
        };
    }
}