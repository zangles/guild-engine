<?php

namespace App\Actions\GuildMember;

use App\Models\Main\GuildMember;
use App\Repositories\GuildMemberRepository;

class UpdateMemberRoleAction
{
    public function __construct(private GuildMemberRepository $repository) {}

    public function handle(GuildMember $member, int $roleId): GuildMember
    {
        return $this->repository->update($member, [
            'guild_role_id' => $roleId,
        ]);
    }
}
