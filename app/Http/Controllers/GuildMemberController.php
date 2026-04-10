<?php

namespace App\Http\Controllers;

use App\ApplicationServices\GuildMember\TransferLeadershipApplicationService;
use App\Enums\GuildPermission;
use App\Exceptions\CannotKickLeaderException;
use App\Exceptions\InsufficientPermissionsException;
use App\Exceptions\MemberAlreadyExistsException;
use App\Finders\GuildMemberFinder;
use App\Http\Requests\GuildMember\UpdateMemberRoleRequest;
use App\Http\Resources\GuildMemberResource;
use App\Models\Main\Guild;
use App\Models\Main\GuildMember;
use App\Services\GuildMemberService;
use App\Services\GuildPermissionGate;
use App\Services\GuildRoleService;
use App\UseCases\GuildMember\ApproveMemberProcess;
use App\UseCases\GuildMember\InviteMemberProcess;
use App\UseCases\GuildMember\JoinGuildProcess;
use App\UseCases\GuildMember\KickMemberProcess;
use App\UseCases\GuildMember\RejectMemberProcess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuildMemberController extends Controller
{
    public function __construct(
        private GuildMemberFinder $memberFinder,
        private GuildMemberService $memberService,
        private GuildPermissionGate $permissionGate,
        private GuildRoleService $roleService,
        private JoinGuildProcess $joinProcess,
        private InviteMemberProcess $inviteProcess,
        private ApproveMemberProcess $approveProcess,
        private RejectMemberProcess $rejectProcess,
        private KickMemberProcess $kickProcess,
        private TransferLeadershipApplicationService $transferService,
    ) {}

    public function me(Guild $guild): JsonResponse
    {
        $member = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());

        if (!$member) {
            return response()->json(['message' => 'No eres miembro activo de este guild.'], 403);
        }

        return response()->json([
            'id'          => $member->id,
            'role'        => $member->role->name,
            'status'      => $member->status,
            'permissions' => $this->roleService->getPermissionSlugs($member->role),
        ]);
    }

    /**
     * @throws InsufficientPermissionsException
     */
    public function index(Guild $guild): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::IsGuildMember);

        $members = $this->memberService->getActiveMembersWithRoles($guild->id);
        return response()->json(GuildMemberResource::collection($members));
    }

    /**
     * @throws MemberAlreadyExistsException
     */
    public function join(Guild $guild): JsonResponse
    {
        $member = $this->joinProcess->execute($guild, auth()->id());
        return response()->json(new GuildMemberResource($member), 201);
    }

    /**
     * @throws MemberAlreadyExistsException
     * @throws InsufficientPermissionsException
     */
    public function invite(Request $request, Guild $guild): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::InviteMembers);

        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        $member = $this->inviteProcess->execute($guild->id, $request->user_id, auth()->id());
        return response()->json(new GuildMemberResource($member), 201);
    }

    /**
     * @throws InsufficientPermissionsException
     */
    public function approve(Guild $guild, GuildMember $member): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ApproveMembers);

        $member = $this->approveProcess->execute($member);
        return response()->json(new GuildMemberResource($member));
    }

    /**
     * @throws InsufficientPermissionsException
     */
    public function reject(Guild $guild, GuildMember $member): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ApproveMembers);

        $member = $this->rejectProcess->execute($member);
        return response()->json(new GuildMemberResource($member));
    }

    /**
     * @throws InsufficientPermissionsException
     * @throws CannotKickLeaderException
     */
    public function kick(Guild $guild, GuildMember $member): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::KickMembers);

        $member = $this->kickProcess->execute($guild, $member);
        return response()->json(new GuildMemberResource($member));
    }

    /**
     * @throws InsufficientPermissionsException
     */
    public function updateRole(UpdateMemberRoleRequest $request, Guild $guild, GuildMember $member): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageRoles);

        $member = $this->memberService->updateRole($member, $request->guild_role_id);
        return response()->json(new GuildMemberResource($member));
    }

    /**
     * @throws InsufficientPermissionsException
     */
    public function transferLeadership(Request $request, Guild $guild): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::TransferLeadership);

        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        $guild = $this->transferService->handle($guild, $request->user_id);
        return response()->json(new \App\Http\Resources\GuildResource($guild));
    }
}
