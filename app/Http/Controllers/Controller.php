<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class Controller
{
    protected function accessibleBranchIds(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        if ($user->hasAnyRole(['owner', 'admin'])) {
            return Branch::where('is_active', true)->pluck('id');
        }

        if ($user->branch_id) {
            return collect([$user->branch_id]);
        }

        return Branch::where('is_active', true)->pluck('id');
    }

    protected function accessibleBranches()
    {
        return Branch::query()
            ->whereIn('id', $this->accessibleBranchIds())
            ->orderBy('name');
    }

    protected function scopeToAccessibleBranches(Builder $query, string $column = 'branch_id'): Builder
    {
        $branchIds = $this->accessibleBranchIds();

        if ($branchIds->isEmpty()) {
            $query->whereRaw('1 = 0');

            return $query;
        }

        return $query->whereIn($column, $branchIds);
    }

    protected function ensureBranchAccess(?int $branchId): void
    {
        if ($branchId && ! $this->accessibleBranchIds()->contains($branchId)) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }
    }
}
