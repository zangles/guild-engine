<?php

namespace GuildEngine\Repositories\Guilds;

use GuildEngine\DTOs\CreateGuildDTO;
use GuildEngine\Models\Guild;

final class GuildsRepository implements GuildsRepositoryInterface
{
    public function save(CreateGuildDTO $dto): Guild
    {
        return Guild::create($dto->toArray());
    }
}
