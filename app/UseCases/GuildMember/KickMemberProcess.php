<?php

namespace App\UseCases\GuildMember;

use App\Exceptions\CannotKickLeaderException;
use App\Models\Main\Guild;
use App\Models\Main\GuildMember;
use App\Services\GuildMemberService;

class KickMemberProcess
{
    public function __construct(private GuildMemberService $memberService) {}

    public function execute(Guild $guild, GuildMember $member): GuildMember
    {
        if ($guild->leader_user_id === $member->user_id) {
            throw new CannotKickLeaderException();
        }

        return $this->memberService->kick($member);
    }
}
