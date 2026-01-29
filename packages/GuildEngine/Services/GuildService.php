<?php

namespace GuildEngine\Services;

use GuildEngine\Actions\Guilds\CreateGuildAction;
use GuildEngine\DTOs\CreateGuildDTO;
use GuildEngine\Exceptions\Guild\GuildNameAlradyExistsException;
use Illuminate\Support\Facades\Auth;

class GuildService
{
    public function __construct(
        private readonly CreateGuildAction $createGuildAction,
    ) {}

    /**
     * @throws GuildNameAlradyExistsException
     */
    public function create(string $name, string $desc)
    {
        $dto = CreateGuildDTO::make($name, $desc, Auth::id());
        return $this->createGuildAction->handle($dto);
    }
}
