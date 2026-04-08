<?php

namespace App\Repositories;

use App\Models\Main\GuildMember;

class GuildMemberRepository
{
    public function create(array $data): GuildMember
    {
        return GuildMember::create($data);
    }

    public function update(GuildMember $member, array $data): GuildMember
    {
        $member->update($data);
        return $member->fresh();
    }
}
