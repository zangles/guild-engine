<?php

namespace App\Actions\GuildMember;

use App\DTO\GuildMember\CreateMemberInviteDTO;
use App\Enums\GuildMemberStatus;
use App\Models\Main\GuildMember;
use App\Repositories\GuildMemberRepository;

class CreateMemberInviteAction
{
    public function __construct(private GuildMemberRepository $repository) {}

    public function handle(CreateMemberInviteDTO $dto): GuildMember
    {
        return $this->repository->create([
            'guild_id'            => $dto->guild_id,
            'user_id'             => $dto->user_id,
            'invited_by_user_id'  => $dto->invited_by_user_id,
            'guild_role_id'       => 0, // será asignado al aprobar
            'status'              => GuildMemberStatus::PendingInvite,
        ]);
    }
}
