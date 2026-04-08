<?php

namespace App\Repositories;

use App\Models\Main\Guild;

class GuildRepository
{
    public function create(array $data): Guild
    {
        return Guild::create($data);
    }

    public function update(Guild $guild, array $data): Guild
    {
        $guild->update($data);
        return $guild->fresh();
    }
}
