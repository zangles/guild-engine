<?php

namespace App\Services;

use App\Actions\Event\CancelEventAction;
use App\Actions\Event\CreateEventAction;
use App\Actions\EventRsvp\UpsertEventRsvpAction;
use App\Actions\EventRsvp\RegisterAttendanceAction;
use App\DTO\Event\CreateEventDTO;
use App\DTO\Event\RegisterAttendanceDTO;
use App\DTO\EventRsvp\UpsertRsvpDTO;
use App\Enums\EventStatus;
use App\Exceptions\EventAlreadyCancelledException;
use App\Finders\EventFinder;
use App\Models\Main\Event;
use App\Models\Main\EventRsvp;
use App\Queries\EventQueries;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class EventService
{
    public function __construct(
        private EventFinder $finder,
        private EventQueries $queries,
        private CreateEventAction $createAction,
        private CancelEventAction $cancelAction,
        private UpsertEventRsvpAction $upsertRsvpAction,
        private RegisterAttendanceAction $registerAttendanceAction,
    ) {}

    public function findByIdOrFail(int $id): Event
    {
        return $this->finder->findByIdOrFail($id);
    }

    public function getGuildEvents(int $guildId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->queries->getGuildEventsWithStatus($guildId, $perPage);
    }

    public function create(CreateEventDTO $dto): Event
    {
        return $this->createAction->handle($dto);
    }

    public function cancel(Event $event): Event
    {
        if ($event->status === EventStatus::Cancelled) {
            throw new EventAlreadyCancelledException();
        }

        return $this->cancelAction->handle($event);
    }

    public function submitRsvp(UpsertRsvpDTO $dto): EventRsvp
    {
        $event = $this->finder->findByIdOrFail($dto->event_id);

        if ($event->status !== EventStatus::Scheduled || $event->starts_at->isPast()) {
            throw new \InvalidArgumentException('RSVP can only be submitted for future scheduled events.');
        }

        return $this->upsertRsvpAction->handle($dto);
    }

    public function registerAttendance(RegisterAttendanceDTO $dto): void
    {
        $event = $this->finder->findByIdOrFail($dto->event_id);

        if ($event->status !== EventStatus::Completed) {
            throw new \InvalidArgumentException('Attendance can only be registered for completed events.');
        }

        $this->registerAttendanceAction->handle($dto);
    }
}
