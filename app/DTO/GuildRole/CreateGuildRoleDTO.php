<?php

namespace App\DTO\GuildRole;

readonly class CreateGuildRoleDTO
{
    public function __construct(
        public int $guild_id,
        public string $name,
        public array $permission_ids,
    ) {}
}
