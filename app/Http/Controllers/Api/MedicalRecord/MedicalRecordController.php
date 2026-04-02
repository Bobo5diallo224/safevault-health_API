<?php

namespace App\Http\Controllers\Api\MedicalRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecord\CreateRecordRequest;
use App\Http\Resources\MedicalRecord\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Services\MedicalRecord\MedicalRecordService;
use App\DTOs\MedicalRecord\CreateRecordDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MedicalRecordController extends Controller
{
    public function __construct(
        private readonly MedicalRecordService $recordService
    ) {}

    public function index(Request $request): ResourceCollection
    {
        $this->authorize('viewAny', MedicalRecord::class);

        $records = $this->recordService->listForUser($request->user(), $request);

        return MedicalRecordResource::collection($records);
    }

    public function show(Request $request, string $id): MedicalRecordResource
    {
        $record = $this->recordService->findOrFail($id, $request->user(), $request);

        $this->authorize('view', $record);

        return new MedicalRecordResource($record);
    }

    public function store(CreateRecordRequest $request): JsonResponse
    {
        $this->authorize('create', MedicalRecord::class);

        $dto    = CreateRecordDTO::fromRequest($request);
        $record = $this->recordService->create($dto, $request->user(), $request);

        return response()->json(new MedicalRecordResource($record), 201);
    }

    public function destroy(Request $request, MedicalRecord $record): JsonResponse
    {
        $this->authorize('delete', $record);

        $this->recordService->delete($record, $request->user(), $request);

        return response()->json(['message' => 'Dossier supprimé.'], 200);
    }
}