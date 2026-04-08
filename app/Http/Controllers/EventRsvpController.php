<?php

namespace App\Http\Controllers;

use App\DTO\EventRsvp\UpsertRsvpDTO;
use App\Http\Requests\EventRsvp\UpsertRsvpRequest;
use App\Http\Resources\EventRsvpResource;
use App\Models\Main\Event;
use App\Models\Main\Guild;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;

class EventRsvpController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function upsert(UpsertRsvpRequest $request, Guild $guild, Event $event): JsonResponse
    {
        $dto = new UpsertRsvpDTO(
            event_id: $event->id,
            user_id:  auth()->id(),
            response: $request->response,
        );

        $rsvp = $this->eventService->submitRsvp($dto);

        return response()->json(new EventRsvpResource($rsvp));
    }
}
