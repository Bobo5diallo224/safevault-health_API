<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\AccessGrant;
use Illuminate\Support\Carbon;

class MedicalRecordPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null; 
    }
    
    public function viewAny(User $user): bool
    {
        return $user->isPatient() || $user->isDoctor();
    }

    
    public function view(User $user, MedicalRecord $record): bool
    {
        
        if ($user->isPatient()) {
            return $record->patient->user_id === $user->id;
        }

        if ($user->isDoctor()) {
            return AccessGrant::query()
                ->where('record_id', $record->id)
                ->where('doctor_id', $user->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
                })
                ->exists();
        }

        return false;
    }

    
    public function create(User $user): bool
    {
        return $user->isDoctor();
    }
    
    public function update(User $user, MedicalRecord $record): bool
    {
        if ($user->isDoctor()) {
            return $record->created_by === $user->id;
        }

        return false;
    }
   
    public function delete(User $user, MedicalRecord $record): bool
    {
        return false; 
    }
}