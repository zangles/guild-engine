<?php

namespace App\UseCases\GuildMember;

use App\Actions\GuildMember\UpdateMemberRoleAction;
use App\Finders\GuildMemberFinder;
use App\Finders\GuildRoleFinder;
use App\Models\Main\Guild;
use App\Repositories\GuildRepository;

class TransferLeadershipProcess
{
    public function __construct(
        private GuildRepository $guildRepository,
        private GuildMemberFinder $memberFinder,
        private GuildRoleFinder $roleFinder,
        private UpdateMemberRoleAction $updateRoleAction,
    ) {}

    public function execute(Guild $guild, int $newLeaderUserId): Guild
    {
        $leaderRole  = $this->roleFinder->findLeaderRoleByGuild($guild->id);
        $officialRole = $this->roleFinder->findOfficialRoleByGuild($guild->id);

        $currentLeaderMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, $guild->leader_user_id);
        $newLeaderMember     = $this->memberFinder->findActiveByGuildAndUser($guild->id, $newLeaderUserId);

        $this->guildRepository->update($guild, ['leader_user_id' => $newLeaderUserId]);

        $this->updateRoleAction->handle($newLeaderMember, $leaderRole->id);
        $this->updateRoleAction->handle($currentLeaderMember, $officialRole->id);

        return $guild->fresh();
    }
}
