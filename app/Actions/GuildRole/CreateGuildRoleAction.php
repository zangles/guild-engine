<?php

namespace App\Actions\GuildRole;

use App\DTO\GuildRole\CreateGuildRoleDTO;
use App\Models\Main\GuildRole;
use App\Repositories\GuildRoleRepository;

class CreateGuildRoleAction
{
    public function __construct(
        private GuildRoleRepository $repository,
        private SyncRolePermissionsAction $syncPermissions,
    ) {}

    public function handle(CreateGuildRoleDTO $dto, bool $isSystem = false): GuildRole
    {
        $role = $this->repository->create([
            'guild_id'  => $dto->guild_id,
            'name'      => $dto->name,
            'is_system' => $isSystem,
        ]);

        if (!empty($dto->permission_ids)) {
            $this->syncPermissions->handle($role, $dto->permission_ids);
        }

        return $role;
    }
}
