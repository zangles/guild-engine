<?php

namespace GuildEngine\Repositories\Guilds;
use GuildEngine\DTOs\CreateGuildDTO;
use GuildEngine\Models\Guild;

interface GuildsRepositoryInterface {
    public function save(CreateGuildDTO $dto): Guild;
}
