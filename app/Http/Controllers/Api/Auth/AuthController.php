<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\AuthService;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\LoginDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto    = RegisterDTO::fromRequest($request);
        $result = $this->authService->register($dto);

        return response()->json(
            new AuthResource($result),
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dto    = LoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);

        return response()->json(new AuthResource($result));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('patientProfile'));
    }
}