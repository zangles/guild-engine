<?php

namespace App\Services;

use App\Actions\GuildMember\ApproveMemberAction;
use App\Actions\GuildMember\CreateMemberInviteAction;
use App\Actions\GuildMember\CreateMemberRequestAction;
use App\Actions\GuildMember\KickMemberAction;
use App\Actions\GuildMember\RejectMemberAction;
use App\Actions\GuildMember\UpdateMemberRoleAction;
use App\DTO\GuildMember\CreateMemberInviteDTO;
use App\DTO\GuildMember\CreateMemberRequestDTO;
use App\Finders\GuildMemberFinder;
use App\Models\Main\GuildMember;
use App\Queries\GuildMemberQueries;
use Illuminate\Database\Eloquent\Collection;

class GuildMemberService
{
    public function __construct(
        private GuildMemberFinder $finder,
        private GuildMemberQueries $queries,
        private CreateMemberRequestAction $requestAction,
        private CreateMemberInviteAction $inviteAction,
        private ApproveMemberAction $approveAction,
        private RejectMemberAction $rejectAction,
        private KickMemberAction $kickAction,
        private UpdateMemberRoleAction $updateRoleAction,
    ) {}

    public function findActiveByGuildAndUser(int $guildId, int $userId): ?GuildMember
    {
        return $this->finder->findActiveByGuildAndUser($guildId, $userId);
    }

    public function findPendingRequests(int $guildId): Collection
    {
        return $this->finder->findPendingRequestsByGuild($guildId);
    }

    public function getActiveMembersWithRoles(int $guildId): Collection
    {
        return $this->queries->getActiveMembersWithRoles($guildId);
    }

    public function requestJoin(CreateMemberRequestDTO $dto): GuildMember
    {
        return $this->requestAction->handle($dto);
    }

    public function invite(CreateMemberInviteDTO $dto): GuildMember
    {
        return $this->inviteAction->handle($dto);
    }

    public function approve(GuildMember $member): GuildMember
    {
        return $this->approveAction->handle($member);
    }

    public function reject(GuildMember $member): GuildMember
    {
        return $this->rejectAction->handle($member);
    }

    public function kick(GuildMember $member): GuildMember
    {
        return $this->kickAction->handle($member);
    }

    public function updateRole(GuildMember $member, int $roleId): GuildMember
    {
        return $this->updateRoleAction->handle($member, $roleId);
    }
}
