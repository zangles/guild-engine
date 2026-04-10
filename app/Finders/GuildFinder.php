<?php

namespace App\Finders;

use App\Enums\GuildMemberStatus;
use App\Models\Main\Guild;
use App\Models\Main\GuildMember;
use App\Models\Main\GuildRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GuildFinder
{
    public function findById(int $id): ?Guild
    {
        return Guild::find($id);
    }

    public function findByIdOrFail(int $id): Guild
    {
        return Guild::findOrFail($id);
    }

    public function getActiveGuildsForUser(int $userId): Collection
    {
        $members = GuildMember::where('user_id', $userId)
            ->where('status', GuildMemberStatus::Active)
            ->get();

        $guilds = Guild::whereIn('id', $members->pluck('guild_id')->unique())->get()->keyBy('id');
        $roles  = GuildRole::whereIn('id', $members->pluck('guild_role_id')->unique())->get()->keyBy('id');

        foreach ($members as $member) {
            $member->setRelation('guild', $guilds[$member->guild_id] ?? null);
            $member->setRelation('role', $roles[$member->guild_role_id] ?? null);
        }

        return $members;
    }
}
