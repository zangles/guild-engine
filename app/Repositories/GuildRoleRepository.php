<?php

namespace App\Repositories;

use App\Models\Main\GuildRole;

class GuildRoleRepository
{
    public function create(array $data): GuildRole
    {
        return GuildRole::create($data);
    }

    public function update(GuildRole $role, array $data): GuildRole
    {
        $role->update($data);
        return $role->fresh();
    }
}
