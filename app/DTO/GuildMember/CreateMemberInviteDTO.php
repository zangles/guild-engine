<?php

namespace App\DTO\GuildMember;

readonly class CreateMemberInviteDTO
{
    public function __construct(
        public int $guild_id,
        public int $user_id,
        public int $invited_by_user_id,
    ) {}
}
