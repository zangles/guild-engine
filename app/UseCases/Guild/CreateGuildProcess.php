<?php

namespace App\UseCases\Guild;

use App\DTO\Guild\CreateGuildDTO;
use App\DTO\GuildMember\CreateMemberRequestDTO;
use App\Finders\GuildRoleFinder;
use App\Models\Main\Guild;
use App\Services\GuildMemberService;
use App\Services\GuildRoleService;
use App\Services\GuildService;

class CreateGuildProcess
{
    public function __construct(
        private GuildService $guildService,
        private GuildRoleService $roleService,
        private GuildMemberService $memberService,
        private GuildRoleFinder $roleFinder,
    ) {}

    public function execute(CreateGuildDTO $dto): Guild
    {
        $guild = $this->guildService->create($dto);

        $this->roleService->createDefaultRoles($guild->id);

        $leaderRole = $this->roleFinder->findLeaderRoleByGuild($guild->id);

        $requestDto = new CreateMemberRequestDTO(
            guild_id:      $guild->id,
            user_id:       $dto->creator_user_id,
            guild_role_id: $leaderRole->id,
        );

        $member = $this->memberService->requestJoin($requestDto);
        $this->memberService->approve($member);

        return $guild->fresh();
    }
}
