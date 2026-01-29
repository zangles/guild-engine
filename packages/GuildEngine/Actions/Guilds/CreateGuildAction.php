<?php

namespace GuildEngine\Actions\Guilds;

use GuildEngine\DTOs\CreateGuildDTO;
use GuildEngine\Models\Guild;
use GuildEngine\Repositories\Guilds\GuildsRepositoryInterface;

final class CreateGuildAction
{
    public function __construct(
        private readonly GuildsRepositoryInterface $guildsRepository
    ) {}

    public function handle(CreateGuildDTO $dto): Guild
    {
        return $this->guildsRepository->save($dto);
    }
}
