<?php

namespace App\UseCases\GuildMember;

use App\Models\Main\GuildMember;
use App\Services\GuildMemberService;

class RejectMemberProcess
{
    public function __construct(private GuildMemberService $memberService) {}

    public function execute(GuildMember $member): GuildMember
    {
        return $this->memberService->reject($member);
    }
}
