<?php

namespace App\UseCases\GuildMember;

use App\DTO\GuildMember\CreateMemberRequestDTO;
use App\Exceptions\MemberAlreadyExistsException;
use App\Finders\GuildMemberFinder;
use App\Finders\GuildRoleFinder;
use App\Models\Main\Guild;
use App\Models\Main\GuildMember;
use App\Services\GuildMemberService;

class JoinGuildProcess
{
    public function __construct(
        private GuildMemberFinder $memberFinder,
        private GuildRoleFinder $roleFinder,
        private GuildMemberService $memberService,
    ) {}

    public function execute(Guild $guild, int $userId): GuildMember
    {
        $existing = $this->memberFinder->findByGuildAndUser($guild->id, $userId);
        if ($existing) {
            throw new MemberAlreadyExistsException();
        }

        $memberRole = $this->roleFinder->findDefaultMemberRoleByGuild($guild->id);

        return $this->memberService->requestJoin(new CreateMemberRequestDTO(
            guild_id:      $guild->id,
            user_id:       $userId,
            guild_role_id: $memberRole->id,
        ));
    }
}
