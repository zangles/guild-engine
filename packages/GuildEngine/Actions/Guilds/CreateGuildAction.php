<?php

namespace GuildEngine\Actions\Guilds;

use GuildEngine\DTOs\CreateGuildDTO;
use GuildEngine\DTOs\GuildDTO;
use GuildEngine\Exceptions\Guild\GuildNameAlradyExistsException;
use GuildEngine\Finders\GuildFinder;
use GuildEngine\Repositories\Guilds\GuildsRepositoryInterface;

final class CreateGuildAction
{
    public function __construct(
        private readonly GuildsRepositoryInterface $guildsRepository,
        private readonly GuildFinder $guildFinder,
    ) {}

    /**
     * @throws GuildNameAlradyExistsException
     */
    public function handle(CreateGuildDTO $dto): GuildDTO
    {
        if ($this->guildFinder->findByName($dto->name) !== null) {
            throw new GuildNameAlradyExistsException();
        }

        $guild = $this->guildsRepository->save($dto);
        return GuildDTO::fromModel($guild);
    }
}
