<?php

namespace App\Actions\GuildMember;

use App\Enums\GuildMemberStatus;
use App\Models\Main\GuildMember;
use App\Repositories\GuildMemberRepository;

class KickMemberAction
{
    public function __construct(private GuildMemberRepository $repository) {}

    public function handle(GuildMember $member): GuildMember
    {
        return $this->repository->update($member, [
            'status' => GuildMemberStatus::Kicked,
        ]);
    }
}
