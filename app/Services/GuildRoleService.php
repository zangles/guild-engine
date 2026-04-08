<?php

namespace App\Services;

use App\Actions\GuildRole\CreateGuildRoleAction;
use App\Actions\GuildRole\SyncRolePermissionsAction;
use App\DTO\GuildRole\CreateGuildRoleDTO;
use App\DTO\GuildRole\UpdateGuildRoleDTO;
use App\Enums\GuildPermission;
use App\Finders\GuildRoleFinder;
use App\Finders\PermissionFinder;
use App\Models\Main\GuildRole;
use Illuminate\Database\Eloquent\Collection;

class GuildRoleService
{
    public function __construct(
        private GuildRoleFinder $finder,
        private PermissionFinder $permissionFinder,
        private CreateGuildRoleAction $createAction,
        private SyncRolePermissionsAction $syncAction,
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
        $leaderSlugs = [
            GuildPermission::ManageEvents->value,
            GuildPermission::ApproveMembers->value,
            GuildPermission::InviteMembers->value,
            GuildPermission::KickMembers->value,
            GuildPermission::ManageDkp->value,
            GuildPermission::ManageDonations->value,
            GuildPermission::RegisterAttendance->value,
            GuildPermission::ViewAuditLog->value,
            GuildPermission::ManageRoles->value,
            GuildPermission::TransferLeadership->value,
        ];

        $officialSlugs = [
            GuildPermission::ManageEvents->value,
            GuildPermission::ApproveMembers->value,
            GuildPermission::InviteMembers->value,
            GuildPermission::KickMembers->value,
            GuildPermission::ManageDkp->value,
            GuildPermission::ManageDonations->value,
            GuildPermission::RegisterAttendance->value,
            GuildPermission::ViewAuditLog->value,
        ];

        $leaderPermissions = $this->permissionFinder->findBySlugs($leaderSlugs)->pluck('id')->toArray();
        $officialPermissions = $this->permissionFinder->findBySlugs($officialSlugs)->pluck('id')->toArray();

        $this->createAction->handle(new CreateGuildRoleDTO($guildId, 'Líder', $leaderPermissions), true);
        $this->createAction->handle(new CreateGuildRoleDTO($guildId, 'Oficial', $officialPermissions), true);
        $this->createAction->handle(new CreateGuildRoleDTO($guildId, 'Miembro', []), true);
    }

    public function createCustomRole(CreateGuildRoleDTO $dto): GuildRole
    {
        return $this->createAction->handle($dto);
    }

    public function updatePermissions(GuildRole $role, UpdateGuildRoleDTO $dto): void
    {
        if ($role->is_system && $role->name === 'Líder') {
            throw new \App\Exceptions\InsufficientPermissionsException();
        }

        $this->syncAction->handle($role, $dto->permission_ids);
    }
}
