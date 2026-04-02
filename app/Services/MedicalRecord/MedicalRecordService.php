<?php

namespace App\Services\MedicalRecord;

use App\DTOs\MedicalRecord\CreateRecordDTO;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicalRecordService
{
    public function listForUser(User $user, Request $request): LengthAwarePaginator
    {
        $query = MedicalRecord::query()->with(['patient.user', 'creator']);

        if ($user->isPatient()) {
            $query->whereHas('patient', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($user->isDoctor()) {
            $query->whereHas('accessGrants', function ($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->where('is_active', true)
                  ->where(function ($inner) {
                      $inner->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                  });
            });
        }

        AuditLog::record('records.listed', $user, $request);

        return $query->latest()->paginate(15);
    }

    public function findOrFail(string $id, User $user, Request $request): MedicalRecord
    {
        $record = MedicalRecord::with(['patient.user', 'creator', 'accessGrants'])
            ->findOrFail($id);

        AuditLog::record('record.viewed', $record, $request);

        return $record;
    }

    public function create(CreateRecordDTO $dto, User $author, Request $request): MedicalRecord
    {
        $record = MedicalRecord::create([
            'patient_id' => $dto->patientId,
            'created_by' => $author->id,
            'title'      => $dto->title,
            'diagnosis'  => $dto->diagnosis,   
            'treatment'  => $dto->treatment,   
            'files_path' => $dto->filesPath,
        ]);

        AuditLog::record(
            action:    'record.created',
            subject:   $record,
            request:   $request,
            newValues: ['title' => $dto->title]
        );

        return $record->load(['patient.user', 'creator']);
    }
    
    public function delete(MedicalRecord $record, User $admin, Request $request): void
    {
        AuditLog::record(
            action:    'record.deleted',
            subject:   $record,
            request:   $request,
            oldValues: ['title' => $record->title]
        );

        $record->delete();
    }
}