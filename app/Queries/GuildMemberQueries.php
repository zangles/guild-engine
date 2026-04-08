<?php

namespace App\Queries;

use App\Enums\GuildMemberStatus;
use App\Models\Main\GuildMember;
use Illuminate\Database\Eloquent\Collection;

class GuildMemberQueries
{
    public function getActiveMembersWithRoles(int $guildId): Collection
    {
        return GuildMember::with(['user', 'role'])
            ->where('guild_id', $guildId)
            ->where('status', GuildMemberStatus::Active)
            ->get();
    }
}
