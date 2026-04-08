<?php

namespace App\Http\Controllers;

use App\DTO\GuildRole\CreateGuildRoleDTO;
use App\DTO\GuildRole\UpdateGuildRoleDTO;
use App\Enums\GuildPermission;
use App\Finders\GuildMemberFinder;
use App\Finders\GuildRoleFinder;
use App\Http\Requests\GuildRole\CreateGuildRoleRequest;
use App\Http\Requests\GuildRole\UpdateGuildRoleRequest;
use App\Http\Resources\GuildRoleResource;
use App\Models\Main\Guild;
use App\Models\Main\GuildRole;
use App\Services\GuildPermissionGate;
use App\Services\GuildRoleService;
use Illuminate\Http\JsonResponse;

class GuildRoleController extends Controller
{
    public function __construct(
        private GuildMemberFinder $memberFinder,
        private GuildRoleFinder $roleFinder,
        private GuildRoleService $roleService,
        private GuildPermissionGate $permissionGate,
    ) {}

    public function index(Guild $guild): JsonResponse
    {
        $roles = $this->roleService->findByGuild($guild->id);
        return response()->json(GuildRoleResource::collection($roles));
    }

    public function store(CreateGuildRoleRequest $request, Guild $guild): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageRoles);

        $dto  = new CreateGuildRoleDTO($guild->id, $request->name, $request->input('permission_ids', []));
        $role = $this->roleService->createCustomRole($dto);

        return response()->json(new GuildRoleResource($role), 201);
    }

    public function update(UpdateGuildRoleRequest $request, Guild $guild, GuildRole $role): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageRoles);

        $dto = new UpdateGuildRoleDTO($request->permission_ids);
        $this->roleService->updatePermissions($role, $dto);

        $role->load('permissions');
        return response()->json(new GuildRoleResource($role));
    }
}
