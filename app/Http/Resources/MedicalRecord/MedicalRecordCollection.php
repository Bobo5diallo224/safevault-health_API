<?php

namespace App\Http\Resources\MedicalRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MedicalRecordCollection extends ResourceCollection
{
   
    public string $collects = MedicalRecordResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => $this->buildMeta($request),
        ];
    }

    
    private function buildMeta(Request $request): array
    {
        $user = $request->user();

        return [
            'pagination' => $this->buildPagination(),

            'abilities' => [
                'can_create' => $user->isDoctor() || $user->isAdmin(),
                'can_delete' => $user->isAdmin(),
                'can_share'  => $user->isPatient(),
                'can_audit'  => $user->isAdmin(),
            ],

            'summary' => $this->buildSummary($user),
        ];
    }

    private function buildPagination(): array
    {
        $paginator = $this->resource;

        if (! method_exists($paginator, 'currentPage')) {
            return [
                'total'        => $paginator->count(),
                'per_page'     => null,
                'current_page' => 1,
                'last_page'    => 1,
                'has_more'     => false,
            ];
        }

        return [
            'total'        => $paginator->total(),
            'per_page'     => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'has_more'     => $paginator->hasMorePages(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ];
    }

    private function buildSummary($user): array
    {
        $items = $this->collection;

        if ($user->isPatient()) {
            return [
                'total_records' => $items->count(),
                'shared_records' => $items->filter(function ($resource) {
                    return $resource->resource
                        ->accessGrants
                        ?->where('is_active', true)
                        ->count() > 0;
                })->count(),
            ];
        }

        if ($user->isDoctor()) {
            return [
                'total_accessible' => $items->count(),
                'expiring_soon' => $items->filter(function ($resource) use ($user) {
                    $grant = $resource->resource
                        ->accessGrants
                        ?->where('doctor_id', $user->id)
                        ->first();

                    return $grant && $grant->expiresSoon();
                })->count(),
            ];
        }

        if ($user->isAdmin()) {
            return [
                'total_records'  => $items->count(),
                'deleted_records' => \App\Models\MedicalRecord::onlyTrashed()->count(),
            ];
        }

        return [];
    }
    
    public function withResponse(Request $request, \Illuminate\Http\JsonResponse $response): void
    {
        $paginator = $this->resource;

        if (method_exists($paginator, 'total')) {
            $response->header('X-Total-Count', $paginator->total());
            $response->header('X-Per-Page', $paginator->perPage());
            $response->header('X-Current-Page', $paginator->currentPage());
        }
    }
}