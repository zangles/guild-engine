<?php

namespace App\Actions\Guild;

use App\DTO\Guild\UpdateGuildDTO;
use App\Models\Main\Guild;
use App\Repositories\GuildRepository;

class UpdateGuildAction
{
    public function __construct(private GuildRepository $repository) {}

    public function handle(Guild $guild, UpdateGuildDTO $dto): Guild
    {
        return $this->repository->update($guild, [
            'name'                           => $dto->name,
            'description'                    => $dto->description,
            'game'                           => $dto->game,
            'is_public'                      => $dto->is_public,
            'dkp_currency_name'              => $dto->dkp_currency_name,
            'discord_webhook_url'            => $dto->discord_webhook_url,
            'discord_advance_notice_minutes' => $dto->discord_advance_notice_minutes,
        ]);
    }
}
