<?php

// app/Services/BillCategoryService.php
namespace App\Services\Owner;

use App\Models\BillCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BillCategoryService
{
    public function paginateForOwner(int $ownerId, ?string $q = null, int $perPage = 12): LengthAwarePaginator
    {
        return BillCategory::query()
            ->where('owner_id', $ownerId)
            ->when($q, fn($qry) => $qry->where('name','like',"%{$q}%"))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createForOwner(int $ownerId, array $data): BillCategory
    {
        return BillCategory::create([
            'owner_id' => $ownerId,
            'name'     => $data['name'],
        ]);
    }

    public function update(BillCategory $category, array $data): BillCategory
    {
        $category->update(['name' => $data['name']]);
        return $category;
    }

    public function delete(BillCategory $category): void
    {
        $category->delete(); // soft delete if enabled
    }
}