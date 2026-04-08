<?php

namespace App\Finders;

use App\Models\Main\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionFinder
{
    public function findAll(): Collection
    {
        return Permission::all();
    }

    public function findBySlug(string $slug): ?Permission
    {
        return Permission::where('slug', $slug)->first();
    }

    public function findByIds(array $ids): Collection
    {
        return Permission::whereIn('id', $ids)->get();
    }

    public function findBySlugs(array $slugs): Collection
    {
        return Permission::whereIn('slug', $slugs)->get();
    }
}
