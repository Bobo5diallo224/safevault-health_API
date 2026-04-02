<?php

namespace App\Jobs;

use App\Models\AccessGrant;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanExpiredAccessGrants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function handle(): void
    {
        Log::info('[CleanExpiredAccessGrants] Démarrage du nettoyage.');

        AccessGrant::query()
            ->where('is_active', true)
            ->where('expires_at', '<=', now())
            ->chunkById(100, function ($grants) {
                foreach ($grants as $grant) {
                    $this->revokeAndLog($grant);
                }
            });

        Log::info('[CleanExpiredAccessGrants] Nettoyage terminé.');
    }

    private function revokeAndLog(AccessGrant $grant): void
    {
        try {
            $oldValues = [
                'doctor_id'  => $grant->doctor_id,
                'record_id'  => $grant->record_id,
                'expires_at' => $grant->expires_at?->toISOString(),
            ];

            $grant->revoke();

            AuditLog::create([
                'user_id'        => null,
                'action'         => 'grant.expired',
                'auditable_type' => AccessGrant::class,
                'auditable_id'   => $grant->id,
                'ip_address'     => null,
                'user_agent'     => 'System/CleanExpiredAccessGrants',
                'old_values'     => $oldValues,
                'new_values'     => ['is_active' => false],
            ]);

        } catch (\Throwable $e) {
            Log::error('[CleanExpiredAccessGrants] Erreur sur grant ' . $grant->id, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('[CleanExpiredAccessGrants] Job échoué définitivement.', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}