<?php

namespace App\Actions\GuildMember;

use App\DTO\GuildMember\CreateMemberRequestDTO;
use App\Enums\GuildMemberStatus;
use App\Models\Main\GuildMember;
use App\Repositories\GuildMemberRepository;

class CreateMemberRequestAction
{
    public function __construct(private GuildMemberRepository $repository) {}

    public function handle(CreateMemberRequestDTO $dto): GuildMember
    {
        return $this->repository->create([
            'guild_id'      => $dto->guild_id,
            'user_id'       => $dto->user_id,
            'guild_role_id' => $dto->guild_role_id,
            'status'        => GuildMemberStatus::PendingRequest,
        ]);
    }
}
