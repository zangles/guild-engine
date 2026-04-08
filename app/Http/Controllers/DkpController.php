<?php

namespace App\Http\Controllers;

use App\ApplicationServices\DkpTransaction\DeductDkpApplicationService;
use App\ApplicationServices\DkpTransaction\GrantDkpApplicationService;
use App\DTO\DkpTransaction\DeductDkpDTO;
use App\DTO\DkpTransaction\GrantDkpDTO;
use App\Enums\GuildPermission;
use App\Exceptions\InsufficientDkpException;
use App\Finders\GuildMemberFinder;
use App\Http\Requests\Dkp\DeductDkpRequest;
use App\Http\Requests\Dkp\GrantDkpRequest;
use App\Http\Resources\DkpBalanceResource;
use App\Http\Resources\DkpTransactionResource;
use App\Models\Main\Guild;
use App\Models\Main\GuildMember;
use App\Services\DkpBalanceService;
use App\Services\DkpTransactionService;
use App\Services\GuildPermissionGate;
use Illuminate\Http\JsonResponse;

class DkpController extends Controller
{
    public function __construct(
        private GuildMemberFinder $memberFinder,
        private GuildPermissionGate $permissionGate,
        private GrantDkpApplicationService $grantService,
        private DeductDkpApplicationService $deductService,
        private DkpBalanceService $balanceService,
        private DkpTransactionService $transactionService,
    ) {}

    public function balance(Guild $guild, GuildMember $member): JsonResponse
    {
        $balance = $this->balanceService->getBalance($guild->id, $member->user_id);
        return response()->json(new DkpBalanceResource($balance));
    }

    public function history(Guild $guild, GuildMember $member): JsonResponse
    {
        $transactions = $this->transactionService->getHistoryWithActors($guild->id, $member->user_id);
        return response()->json(DkpTransactionResource::collection($transactions)->response()->getData(true));
    }

    public function grant(GrantDkpRequest $request, Guild $guild, GuildMember $member): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageDkp);

        $dto = new GrantDkpDTO(
            guild_id:       $guild->id,
            target_user_id: $member->user_id,
            actor_user_id:  auth()->id(),
            amount:         $request->amount,
            reason:         $request->reason,
        );

        $transaction = $this->grantService->handle($dto);

        return response()->json(new DkpTransactionResource($transaction), 201);
    }

    public function deduct(DeductDkpRequest $request, Guild $guild, GuildMember $member): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageDkp);

        $dto = new DeductDkpDTO(
            guild_id:       $guild->id,
            target_user_id: $member->user_id,
            actor_user_id:  auth()->id(),
            amount:         $request->amount,
            reason:         $request->reason,
        );

        try {
            $transaction = $this->deductService->handle($dto);
            return response()->json(new DkpTransactionResource($transaction), 201);
        } catch (InsufficientDkpException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
