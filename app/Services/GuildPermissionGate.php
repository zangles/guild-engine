<?php

namespace App\Services;

use App\Enums\GuildPermission;
use App\Exceptions\InsufficientPermissionsException;
use App\Models\Main\GuildMember;

class GuildPermissionGate
{
    public function authorize(?GuildMember $member, GuildPermission $permission): void
    {
        if (!$member || !$member->role->hasPermission($permission)) {
            throw new InsufficientPermissionsException();
        }
    }
}
