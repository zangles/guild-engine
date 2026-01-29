<?php

namespace GuildEngine\Finders;

use GuildEngine\Models\Guild;

class GuildFinder
{
    public function findById(int $id): ?Guild
    {
        return Guild::find($id);
    }

    public function findByName(string $name): ?Guild
    {
        return Guild::where('name', $name)->first();
    }

    public function findByOwnerId(int $ownerId): ?Guild
    {
        return Guild::where('user_id', $ownerId)->first();
    }
}
