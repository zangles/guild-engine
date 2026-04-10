<?php

namespace App\Services;

use App\Enums\GuildPermission;
use App\Exceptions\InsufficientPermissionsException;
use App\Models\Main\GuildMember;

class GuildPermissionGate
{
    public function __construct(private GuildRoleService $roleService) {}

    public function authorize(?GuildMember $member, GuildPermission $permission): void
    {
        if (!$member || !$this->roleService->hasPermission($member->role, $permission)) {
            throw new InsufficientPermissionsException();
        }
    }
}
