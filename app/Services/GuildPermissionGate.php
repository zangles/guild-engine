<?php

namespace App\Services;

use App\Enums\GuildPermission;
use App\Exceptions\InsufficientPermissionsException;
use App\Models\Main\GuildMember;

class GuildPermissionGate
{
    public function authorize(GuildMember $member, GuildPermission $permission): void
    {
        // $member debe venir con role.permissions eager-loaded
        $hasPermission = $member->role->permissions
            ->contains('slug', $permission->value);

        if (!$hasPermission) {
            throw new InsufficientPermissionsException();
        }
    }
}
