<?php

namespace App\Services;

use App\Actions\Guild\CreateGuildAction;
use App\Actions\Guild\UpdateGuildAction;
use App\DTO\Guild\CreateGuildDTO;
use App\DTO\Guild\UpdateGuildDTO;
use App\Finders\GuildFinder;
use App\Models\Main\Guild;
use App\Queries\GuildQueries;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GuildService
{
    public function __construct(
        private GuildFinder $finder,
        private GuildQueries $queries,
        private CreateGuildAction $createAction,
        private UpdateGuildAction $updateAction,
    ) {}

    public function findByIdOrFail(int $id): Guild
    {
        return $this->finder->findByIdOrFail($id);
    }

    public function searchPublic(?string $name, ?string $game, int $perPage = 15): LengthAwarePaginator
    {
        return $this->queries->searchPublicGuilds($name, $game, $perPage);
    }

    public function getPublicProfile(int $guildId): array
    {
        return $this->queries->getPublicProfile($guildId);
    }

    public function create(CreateGuildDTO $dto): Guild
    {
        return $this->createAction->handle($dto);
    }

    public function update(Guild $guild, UpdateGuildDTO $dto): Guild
    {
        return $this->updateAction->handle($guild, $dto);
    }

    public function getActiveGuildsForUser(int $userId): Collection
    {
        return $this->finder->getActiveGuildsForUser($userId);
    }
}
