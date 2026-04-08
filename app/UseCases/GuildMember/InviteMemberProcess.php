<?php

namespace App\UseCases\GuildMember;

use App\DTO\GuildMember\CreateMemberInviteDTO;
use App\Exceptions\MemberAlreadyExistsException;
use App\Finders\GuildMemberFinder;
use App\Finders\UserFinder;
use App\Models\Main\GuildMember;
use App\Services\GuildMemberService;

class InviteMemberProcess
{
    public function __construct(
        private UserFinder $userFinder,
        private GuildMemberFinder $memberFinder,
        private GuildMemberService $memberService,
    ) {}

    public function execute(int $guildId, int $targetUserId, int $invitedByUserId): GuildMember
    {
        $this->userFinder->findById($targetUserId) ?? throw new \InvalidArgumentException('User not found.');

        $existing = $this->memberFinder->findByGuildAndUser($guildId, $targetUserId);
        if ($existing) {
            throw new MemberAlreadyExistsException();
        }

        return $this->memberService->invite(new CreateMemberInviteDTO(
            guild_id:            $guildId,
            user_id:             $targetUserId,
            invited_by_user_id:  $invitedByUserId,
        ));
    }
}
