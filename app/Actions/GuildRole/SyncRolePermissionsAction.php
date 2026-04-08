<?php

namespace App\Actions\GuildRole;

use App\Models\Main\GuildRole;
use App\Repositories\GuildRolePermissionRepository;

class SyncRolePermissionsAction
{
    public function __construct(private GuildRolePermissionRepository $repository) {}

    public function handle(GuildRole $role, array $permissionIds): void
    {
        $this->repository->sync($role, $permissionIds);
    }
}
