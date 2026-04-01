<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class MedicalRecord extends Model implements Auditable
{
    
    use HasUlids, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'patient_id',
        'created_by',
        'title',
        'diagnosis',
        'treatment',
        'files_path',
    ];

    protected function casts(): array
    {
        return [
            'diagnosis' => 'encrypted',
            'treatment' => 'encrypted',
        ];
    }

    protected array $auditExclude = [
        'diagnosis',
        'treatment',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function accessGrants(): HasMany
    {
        return $this->hasMany(AccessGrant::class, 'record_id');
    }
}