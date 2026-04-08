<?php

namespace App\Queries;

use App\Models\Main\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventQueries
{
    public function getGuildEventsWithStatus(int $guildId, int $perPage = 15): LengthAwarePaginator
    {
        return Event::where('guild_id', $guildId)
            ->orderByDesc('starts_at')
            ->paginate($perPage);
    }
}
