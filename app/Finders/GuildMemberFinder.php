<?php

namespace App\Finders;

use App\Enums\GuildMemberStatus;
use App\Models\Main\GuildMember;
use App\Models\Main\GuildRole;
use Illuminate\Database\Eloquent\Collection;

class GuildMemberFinder
{
    public function findActiveByGuildAndUser(int $guildId, int $userId): ?GuildMember
    {
        $member = GuildMember::where('guild_id', $guildId)
            ->where('user_id', $userId)
            ->where('status', GuildMemberStatus::Active)
            ->first();

        if ($member) {
            $member->setRelation('role', GuildRole::find($member->guild_role_id));
        }

        return $member;
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
