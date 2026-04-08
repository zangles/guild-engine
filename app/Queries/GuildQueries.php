<?php

namespace App\Queries;

use App\Models\Main\Guild;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GuildQueries
{
    public function searchPublicGuilds(?string $name, ?string $game, int $perPage = 15): LengthAwarePaginator
    {
        return Guild::where('is_public', true)
            ->when($name, fn ($q) => $q->where('name', 'like', "%{$name}%"))
            ->when($game, fn ($q) => $q->where('game', $game))
            ->paginate($perPage);
    }

    public function getPublicProfile(int $guildId): array
    {
        $guild = Guild::withCount(['members' => fn ($q) => $q->where('status', 'active')])
            ->findOrFail($guildId);

        return [
            'id'            => $guild->id,
            'name'          => $guild->name,
            'description'   => $guild->description,
            'game'          => $guild->game,
            'members_count' => $guild->members_count,
            'created_at'    => $guild->created_at,
        ];
    }
}
