<?php

namespace App\Repositories;

use App\Models\Main\GuildRole;

class GuildRolePermissionRepository
{
    public function sync(GuildRole $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
    }
}
