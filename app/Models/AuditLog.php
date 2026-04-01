<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasUlids;
   
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
    ];

    protected function casts(): array
    {
        return [
            'old_values'  => 'array',
            'new_values'  => 'array',
            'created_at'  => 'datetime',
        ];
    }

   
    protected static function booted(): void
    {
        static::updating(function (AuditLog $log) {
            throw new \RuntimeException(
                'AuditLog records are immutable. Record ID: ' . $log->id
            );
        });

        static::deleting(function (AuditLog $log) {
            throw new \RuntimeException(
                'AuditLog records cannot be deleted. Record ID: ' . $log->id
            );
        });
    }

   
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }
  
  
    public static function record(
        string  $action,
        Model   $subject,
        \Illuminate\Http\Request $request,
        array   $oldValues = [],
        array   $newValues = []
    ): self {
        return static::create([
            'user_id'         => $request->user()?->id,
            'action'          => $action,
            'auditable_type'  => get_class($subject),
            'auditable_id'    => $subject->getKey(),
            'ip_address'      => $request->ip(),
            'user_agent'      => mb_substr($request->userAgent() ?? '', 0, 500),
            'old_values'      => $oldValues,
            'new_values'      => $newValues,
        ]);
    }
   
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForSubject($query, Model $subject)
    {
        return $query
            ->where('auditable_type', get_class($subject))
            ->where('auditable_id', $subject->getKey());
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }
}