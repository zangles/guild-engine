<?php

namespace App\DTO\GuildMember;

readonly class CreateMemberRequestDTO
{
    public function __construct(
        public int $guild_id,
        public int $user_id,
        public int $guild_role_id,
    ) {}
}
