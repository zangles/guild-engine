<?php

namespace App\DTO\GuildMember;

readonly class UpdateMemberRoleDTO
{
    public function __construct(
        public int $guild_member_id,
        public int $guild_role_id,
    ) {}
}
