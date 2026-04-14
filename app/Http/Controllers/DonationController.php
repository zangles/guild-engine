<?php

namespace App\Http\Controllers;

use App\ApplicationServices\Donation\ReviewDonationApplicationService;
use App\DTO\Donation\CreateDonationDTO;
use App\DTO\Donation\ReviewDonationDTO;
use App\Enums\GuildPermission;
use App\Exceptions\DonationNotPendingException;
use App\Finders\GuildMemberFinder;
use App\Http\Requests\Donation\CreateDonationRequest;
use App\Http\Requests\Donation\ReviewDonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\Main\Donation;
use App\Models\Main\Guild;
use App\Models\Main\User;
use App\Services\DonationService;
use App\Services\GuildPermissionGate;
use Illuminate\Http\JsonResponse;

class DonationController extends Controller
{
    public function __construct(
        private GuildMemberFinder $memberFinder,
        private GuildPermissionGate $permissionGate,
        private DonationService $donationService,
        private ReviewDonationApplicationService $reviewService,
    ) {}

    public function index(Guild $guild): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageDonations);

        $donations = $this->donationService->findPendingByGuild($guild->id);
        return response()->json(DonationResource::collection($donations));
    }

    public function history(Guild $guild): JsonResponse
    {
        $donations = $this->donationService->getApprovedHistory($guild->id);
        return response()->json(DonationResource::collection($donations)->response()->getData(true));
    }

    public function show(Guild $guild, Donation $donation): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());

        if ($donation->user_id !== auth()->id()) {
            $this->permissionGate->authorize($actorMember, GuildPermission::ManageDonations);
        }

        $donation->setRelation('donor', User::find($donation->user_id));

        return response()->json(new DonationResource($donation));
    }

    public function store(CreateDonationRequest $request, Guild $guild): JsonResponse
    {
        $dto = new CreateDonationDTO(
            guild_id: $guild->id,
            user_id:  auth()->id(),
            amount:   $request->amount,
            note:     $request->note,
        );

        $donation = $this->donationService->create($dto);

        return response()->json(new DonationResource($donation), 201);
    }

    public function review(ReviewDonationRequest $request, Guild $guild, Donation $donation): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageDonations);

        $dto = new ReviewDonationDTO(
            donation_id:      $donation->id,
            reviewer_user_id: auth()->id(),
            decision:         $request->decision,
        );

        try {
            $donation = $this->reviewService->handle($dto);
            return response()->json(new DonationResource($donation));
        } catch (DonationNotPendingException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }
}
