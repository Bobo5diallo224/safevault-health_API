<?php

namespace App\Policies;

use App\Models\AccessGrant;
use App\Models\MedicalRecord;
use App\Models\User;

class AccessGrantPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function create(User $user, MedicalRecord $record): bool
    {
        if (! $user->isPatient()) {
            return false;
        }
        
        return $record->patient->user_id === $user->id;
    }

    public function revoke(User $user, AccessGrant $grant): bool
    {
        if ($user->isPatient()) {
            return $grant->granted_by === $user->id;
        }

        return false;
    }
   
    public function viewAny(User $user, MedicalRecord $record): bool
    {
        if ($user->isPatient()) {
            return $record->patient->user_id === $user->id;
        }

        return false;
    }
}