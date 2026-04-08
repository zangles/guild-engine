<?php

namespace App\Http\Controllers;

use App\Enums\GuildPermission;
use App\Finders\GuildMemberFinder;
use App\Http\Resources\AuditLogResource;
use App\Models\Main\Guild;
use App\Services\AuditLogService;
use App\Services\GuildPermissionGate;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    public function __construct(
        private GuildMemberFinder $memberFinder,
        private GuildPermissionGate $permissionGate,
        private AuditLogService $auditLogService,
    ) {}

    public function index(Guild $guild): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ViewAuditLog);

        $logs = $this->auditLogService->getByGuild($guild->id);
        return response()->json(AuditLogResource::collection($logs)->response()->getData(true));
    }
}
