<?php

namespace App\Finders;

use App\Enums\GuildMemberStatus;
use App\Models\Main\GuildMember;
use Illuminate\Database\Eloquent\Collection;

class GuildMemberFinder
{
    public function findActiveByGuildAndUser(int $guildId, int $userId): ?GuildMember
    {
        return GuildMember::with('role')
            ->where('guild_id', $guildId)
            ->where('user_id', $userId)
            ->where('status', GuildMemberStatus::Active)
            ->first();
    }

    public function findByGuildAndUser(int $guildId, int $userId): ?GuildMember
    {
        return GuildMember::where('guild_id', $guildId)
            ->where('user_id', $userId)
            ->first();
    }

    public function findPendingRequestsByGuild(int $guildId): Collection
    {
        return GuildMember::where('guild_id', $guildId)
            ->where('status', GuildMemberStatus::PendingRequest)
            ->get();
    }

    public function findActiveMembersByGuild(int $guildId): Collection
    {
        return GuildMember::where('guild_id', $guildId)
            ->where('status', GuildMemberStatus::Active)
            ->get();
    }

    public function findById(int $id): ?GuildMember
    {
        return GuildMember::find($id);
    }
}
