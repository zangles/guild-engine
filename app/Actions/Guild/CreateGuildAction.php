<?php

namespace App\Actions\Guild;

use App\DTO\Guild\CreateGuildDTO;
use App\Models\Main\Guild;
use App\Repositories\GuildRepository;

class CreateGuildAction
{
    public function __construct(private GuildRepository $repository) {}

    public function handle(CreateGuildDTO $dto): Guild
    {
        return $this->repository->create([
            'name'            => $dto->name,
            'description'     => $dto->description,
            'game'            => $dto->game,
            'is_public'       => $dto->is_public,
            'leader_user_id'  => $dto->creator_user_id,
            'dkp_currency_name' => 'DKP',
        ]);
    }
}
