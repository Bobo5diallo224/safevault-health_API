<?php

namespace App\DTOs\MedicalRecord;

use App\Http\Requests\MedicalRecord\CreateGrantRequest;
use Illuminate\Support\Carbon;

final readonly class CreateGrantDTO
{
    public function __construct(
        public string  $recordId,
        public string  $doctorId,
        public ?Carbon $expiresAt,
    ) {}

    public static function fromRequest(CreateGrantRequest $request, string $recordId): self
    {
        return new self(
            recordId:  $recordId,
            doctorId:  $request->validated('doctor_id'),
            expiresAt: $request->validated('expires_at')
                ? Carbon::parse($request->validated('expires_at'))
                : null,
        );
    }
}