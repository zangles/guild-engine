<?php

namespace App\Http\Controllers;

use App\ApplicationServices\Event\CreateEventApplicationService;
use App\DTO\Event\CreateEventDTO;
use App\DTO\Event\RegisterAttendanceDTO;
use App\Enums\GuildPermission;
use App\Finders\GuildMemberFinder;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\EventRsvp\RegisterAttendanceRequest;
use App\Http\Resources\EventResource;
use App\Models\Main\Event;
use App\Models\Main\Guild;
use App\Services\EventService;
use App\Services\GuildPermissionGate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    public function __construct(
        private GuildMemberFinder $memberFinder,
        private EventService $eventService,
        private GuildPermissionGate $permissionGate,
        private CreateEventApplicationService $createService,
    ) {}

    public function index(Guild $guild): JsonResponse
    {
        $events = $this->eventService->getGuildEvents($guild->id);
        return response()->json(EventResource::collection($events)->response()->getData(true));
    }

    public function store(CreateEventRequest $request, Guild $guild): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageEvents);

        $dto = new CreateEventDTO(
            guild_id:           $guild->id,
            created_by_user_id: auth()->id(),
            title:              $request->title,
            description:        $request->description,
            starts_at:          Carbon::parse($request->starts_at),
            max_attendees:      $request->max_attendees,
        );

        $event = $this->createService->handle($dto, $guild);

        return response()->json(new EventResource($event), 201);
    }

    public function cancel(Guild $guild, Event $event): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::ManageEvents);

        $event = $this->eventService->cancel($event);

        return response()->json(new EventResource($event));
    }

    public function registerAttendance(RegisterAttendanceRequest $request, Guild $guild, Event $event): JsonResponse
    {
        $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
        $this->permissionGate->authorize($actorMember, GuildPermission::RegisterAttendance);

        $dto = new RegisterAttendanceDTO($event->id, $request->attendances);
        $this->eventService->registerAttendance($dto);

        return response()->json(['message' => 'Attendance registered successfully.']);
    }
}
