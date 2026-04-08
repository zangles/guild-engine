<?php

namespace App\Services;

use App\Actions\EventRsvp\RegisterAttendanceAction;
use App\Actions\EventRsvp\UpsertEventRsvpAction;
use App\DTO\Event\RegisterAttendanceDTO;
use App\DTO\EventRsvp\UpsertRsvpDTO;
use App\Finders\EventRsvpFinder;
use App\Models\Main\EventRsvp;
use Illuminate\Database\Eloquent\Collection;

class EventRsvpService
{
    public function __construct(
        private EventRsvpFinder $finder,
        private UpsertEventRsvpAction $upsertAction,
        private RegisterAttendanceAction $registerAction,
    ) {}

    public function findByEvent(int $eventId): Collection
    {
        return $this->finder->findByEvent($eventId);
    }

    public function upsertRsvp(UpsertRsvpDTO $dto): EventRsvp
    {
        return $this->upsertAction->handle($dto);
    }

    public function registerAttendance(RegisterAttendanceDTO $dto): void
    {
        $this->registerAction->handle($dto);
    }
}
