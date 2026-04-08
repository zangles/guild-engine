<?php

namespace App\UseCases\GuildMember;

use App\Actions\GuildMember\UpdateMemberRoleAction;
use App\Finders\GuildRoleFinder;
use App\Models\Main\GuildMember;
use App\Services\GuildMemberService;

class ApproveMemberProcess
{
    public function __construct(
        private GuildMemberService $memberService,
        private GuildRoleFinder $roleFinder,
        private UpdateMemberRoleAction $updateRoleAction,
    ) {}

    public function execute(GuildMember $member): GuildMember
    {
        $approved = $this->memberService->approve($member);

        $memberRole = $this->roleFinder->findDefaultMemberRoleByGuild($member->guild_id);

        return $this->updateRoleAction->handle($approved, $memberRole->id);
    }
}
