<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AccessGrant extends Model
{
    use HasUlids;

    protected $fillable = [
        'record_id',
        'doctor_id',
        'granted_by',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime', 
            'is_active'  => 'boolean',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'record_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', Carbon::now());
            });
    }

    public function scopeExpired($query)
    {
        return $query
            ->where('expires_at', '<=', Carbon::now());
    }

    public function scopeForDoctor($query, string $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function revoke(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function expiresSoon(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isBetween(Carbon::now(), Carbon::now()->addDay());
    }
}