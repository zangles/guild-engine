<?php

namespace App\Finders;

use App\Models\Main\GuildRole;
use Illuminate\Database\Eloquent\Collection;

class GuildRoleFinder
{
    public function findById(int $id): ?GuildRole
    {
        return GuildRole::find($id);
    }

    public function findByGuild(int $guildId): Collection
    {
        return GuildRole::with('permissions')
            ->where('guild_id', $guildId)
            ->get();
    }

    public function findLeaderRoleByGuild(int $guildId): GuildRole
    {
        return GuildRole::where('guild_id', $guildId)
            ->where('name', 'Líder')
            ->where('is_system', true)
            ->firstOrFail();
    }

    public function findOfficialRoleByGuild(int $guildId): GuildRole
    {
        return GuildRole::where('guild_id', $guildId)
            ->where('name', 'Oficial')
            ->where('is_system', true)
            ->firstOrFail();
    }

    public function findDefaultMemberRoleByGuild(int $guildId): GuildRole
    {
        return GuildRole::where('guild_id', $guildId)
            ->where('name', 'Miembro')
            ->where('is_system', true)
            ->firstOrFail();
    }
}
