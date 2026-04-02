<?php

namespace App\Services\MedicalRecord;

use App\DTOs\MedicalRecord\CreateGrantDTO;
use App\Models\AccessGrant;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AccessGrantService
{
    public function grant(CreateGrantDTO $dto, User $patient, Request $request): AccessGrant
    {
        $existingGrant = AccessGrant::query()
            ->where('record_id', $dto->recordId)
            ->where('doctor_id', $dto->doctorId)
            ->where('is_active', true)
            ->first();

        if ($existingGrant && $existingGrant->isValid()) {
            throw ValidationException::withMessages([
                'doctor_id' => 'Ce médecin a déjà un accès actif à ce dossier.',
            ]);
        }

        if ($existingGrant) {
            $existingGrant->update([
                'is_active'  => true,
                'expires_at' => $dto->expiresAt,
                'granted_by' => $patient->id,
            ]);

            AuditLog::record('grant.renewed', $existingGrant, $request);

            return $existingGrant;
        }

        $grant = AccessGrant::create([
            'record_id'  => $dto->recordId,
            'doctor_id'  => $dto->doctorId,
            'granted_by' => $patient->id,
            'expires_at' => $dto->expiresAt,
            'is_active'  => true,
        ]);

        AuditLog::record(
            action:    'grant.created',
            subject:   $grant,
            request:   $request,
            newValues: [
                'doctor_id'  => $dto->doctorId,
                'expires_at' => $dto->expiresAt?->toISOString(),
            ]
        );

        return $grant->load(['doctor', 'medicalRecord']);
    }

    public function revoke(AccessGrant $grant, User $revokedBy, Request $request): void
    {
        AuditLog::record(
            action:    'grant.revoked',
            subject:   $grant,
            request:   $request,
            oldValues: [
                'doctor_id'  => $grant->doctor_id,
                'expires_at' => $grant->expires_at?->toISOString(),
            ]
        );

        $grant->revoke();
    }
}