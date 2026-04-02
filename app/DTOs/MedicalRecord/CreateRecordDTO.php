<?php

namespace App\DTOs\MedicalRecord;

use App\Http\Requests\MedicalRecord\CreateRecordRequest;

final readonly class CreateRecordDTO
{
    public function __construct(
        public string  $patientId,
        public string  $title,
        public string  $diagnosis,
        public string  $treatment,
        public ?string $filesPath = null,
    ) {}

    public static function fromRequest(CreateRecordRequest $request): self
    {
        return new self(
            patientId: $request->validated('patient_id'),
            title:     $request->validated('title'),
            diagnosis: $request->validated('diagnosis'),
            treatment: $request->validated('treatment'),
            filesPath: $request->validated('files_path'),
        );
    }
}