<?php

namespace App\Services;

use App\Actions\GuildRole\CreateGuildRoleAction;
use App\DTO\GuildRole\CreateGuildRoleDTO;
use App\DTO\GuildRole\UpdateGuildRoleDTO;
use App\Enums\GuildPermission;
use App\Enums\SystemRole;
use App\Finders\GuildRoleFinder;
use App\Models\Main\GuildRole;
use Illuminate\Database\Eloquent\Collection;

class GuildRoleService
{
    public function __construct(
        private GuildRoleFinder $finder,
        private CreateGuildRoleAction $createAction,
    ) {}

    public function findByGuild(int $guildId): Collection
    {
        return $this->finder->findByGuild($guildId);
    }

    public function findById(int $id): GuildRole
    {
        return $this->finder->findById($id);
    }

    public function createDefaultRoles(int $guildId): void
    {
        // System role permissions are defined in the SystemRole enum — no DB sync needed
        $this->createAction->handle(new CreateGuildRoleDTO($guildId, 'Líder'), true);
        $this->createAction->handle(new CreateGuildRoleDTO($guildId, 'Oficial'), true);
        $this->createAction->handle(new CreateGuildRoleDTO($guildId, 'Miembro'), true);
    }

    public function createCustomRole(CreateGuildRoleDTO $dto): GuildRole
    {
        return $this->createAction->handle($dto);
    }

    public function hasPermission(GuildRole $role, GuildPermission $permission): bool
    {
        if ($role->is_system) {
            return in_array($permission, SystemRole::from($role->name)->permissions());
        }

        return in_array($permission->value, $role->permissions ?? []);
    }

    public function getPermissionSlugs(GuildRole $role): array
    {
        if ($role->is_system) {
            return array_map(fn ($p) => $p->value, SystemRole::from($role->name)->permissions());
        }

        return $role->permissions ?? [];
    }

    public function updatePermissions(GuildRole $role, UpdateGuildRoleDTO $dto): void
    {
        if ($role->is_system && $role->name === 'Líder') {
            throw new \App\Exceptions\InsufficientPermissionsException();
        }

        $role->update(['permissions' => $dto->permission_slugs]);
    }
}