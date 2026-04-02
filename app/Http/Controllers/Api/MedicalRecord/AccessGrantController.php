<?php

namespace App\Http\Controllers\Api\MedicalRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecord\CreateGrantRequest;
use App\Http\Resources\MedicalRecord\AccessGrantResource;
use App\Models\AccessGrant;
use App\Models\MedicalRecord;
use App\Services\MedicalRecord\AccessGrantService;
use App\DTOs\MedicalRecord\CreateGrantDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessGrantController extends Controller
{
    public function __construct(
        private readonly AccessGrantService $grantService
    ) {}

    public function index(Request $request, MedicalRecord $record): JsonResponse
    {
        $this->authorize('viewAny', [AccessGrant::class, $record]);

        $grants = $record->accessGrants()
            ->with('doctor')
            ->active()
            ->get();

        return response()->json(AccessGrantResource::collection($grants));
    }

    public function store(CreateGrantRequest $request, MedicalRecord $record): JsonResponse
    {
        $this->authorize('create', [AccessGrant::class, $record]);

        $dto   = CreateGrantDTO::fromRequest($request, $record->id);
        $grant = $this->grantService->grant($dto, $request->user(), $request);

        return response()->json(new AccessGrantResource($grant), 201);
    }

    public function revoke(Request $request, AccessGrant $grant): JsonResponse
    {
        $this->authorize('revoke', $grant);

        $this->grantService->revoke($grant, $request->user(), $request);

        return response()->json(['message' => 'Accès révoqué.']);
    }
}