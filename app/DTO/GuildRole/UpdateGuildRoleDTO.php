<?php

namespace App\DTO\GuildRole;

readonly class UpdateGuildRoleDTO
{
    public function __construct(
        public array $permission_slugs,
    ) {}
}
