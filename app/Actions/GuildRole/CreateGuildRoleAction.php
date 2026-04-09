<?php

namespace App\Actions\GuildRole;

use App\DTO\GuildRole\CreateGuildRoleDTO;
use App\Models\Main\GuildRole;
use App\Repositories\GuildRoleRepository;

class CreateGuildRoleAction
{
    public function __construct(private GuildRoleRepository $repository) {}

    public function handle(CreateGuildRoleDTO $dto, bool $isSystem = false): GuildRole
    {
        return $this->repository->create([
            'guild_id'    => $dto->guild_id,
            'name'        => $dto->name,
            'is_system'   => $isSystem,
            'permissions' => $isSystem ? null : ($dto->permission_slugs ?: null),
        ]);
    }
}
