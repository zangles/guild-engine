<?php

namespace App\Queries;

use App\Enums\GuildMemberStatus;
use App\Models\Main\GuildMember;
use App\Models\Main\GuildRole;
use App\Models\Main\User;
use Illuminate\Database\Eloquent\Collection;

class GuildMemberQueries
{
    public function getActiveMembersWithRoles(int $guildId): Collection
    {
        $members = GuildMember::where('guild_id', $guildId)
            ->where('status', GuildMemberStatus::Active)
            ->get();

        $users = User::whereIn('id', $members->pluck('user_id')->unique())->get()->keyBy('id');
        $roles = GuildRole::whereIn('id', $members->pluck('guild_role_id')->unique())->get()->keyBy('id');

        foreach ($members as $member) {
            $member->setRelation('user', $users[$member->user_id] ?? null);
            $member->setRelation('role', $roles[$member->guild_role_id] ?? null);
        }

        return $members;
    }
}
