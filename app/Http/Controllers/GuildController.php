<?php

namespace App\Http\Controllers;

use App\ApplicationServices\Guild\CreateGuildApplicationService;
use App\DTO\Guild\CreateGuildDTO;
use App\DTO\Guild\UpdateGuildDTO;
use App\Http\Requests\Guild\CreateGuildRequest;
use App\Http\Requests\Guild\UpdateGuildRequest;
use App\Http\Resources\GuildMemberResource;
use App\Http\Resources\GuildResource;
use App\Models\Main\Guild;
use App\Services\GuildMemberService;
use App\Services\GuildService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuildController extends Controller
{
    public function __construct(
        private GuildService $guildService,
        private GuildMemberService $memberService,
        private CreateGuildApplicationService $createService,
    ) {}

    public function myGuilds(): JsonResponse
    {
        $memberships = $this->guildService->getActiveGuildsForUser(auth()->id());
        return response()->json(GuildMemberResource::collection($memberships));
    }

    public function index(Request $request): JsonResponse
    {
        $guilds = $this->guildService->searchPublic(
            $request->query('name'),
            $request->query('game'),
            (int) $request->query('per_page', 15),
        );

        return response()->json(GuildResource::collection($guilds)->response()->getData(true));
    }

    public function show(int $guildId): JsonResponse
    {
        $profile = $this->guildService->getPublicProfile($guildId);
        return response()->json($profile);
    }

    public function store(CreateGuildRequest $request): JsonResponse
    {
        $dto = new CreateGuildDTO(
            name:            $request->name,
            description:     $request->description,
            game:            $request->game,
            is_public:       $request->boolean('is_public', true),
            creator_user_id: auth()->id(),
        );

        $guild = $this->createService->handle($dto);

        return response()->json(new GuildResource($guild), 201);
    }

    public function update(UpdateGuildRequest $request, Guild $guild): JsonResponse
    {
        $dto = new UpdateGuildDTO(
            name:                           $request->name,
            description:                    $request->description,
            game:                           $request->game,
            is_public:                      $request->boolean('is_public', true),
            dkp_currency_name:              $request->input('dkp_currency_name', 'DKP'),
            discord_webhook_url:            $request->discord_webhook_url,
            discord_advance_notice_minutes: $request->discord_advance_notice_minutes,
        );

        $guild = $this->guildService->update($guild, $dto);

        return response()->json(new GuildResource($guild));
    }
}
