<?php

namespace App\Finders;

use App\Models\Main\EventRsvp;
use Illuminate\Database\Eloquent\Collection;

class EventRsvpFinder
{
    public function findByEventAndUser(int $eventId, int $userId): ?EventRsvp
    {
        return EventRsvp::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();
    }

    public function findByEvent(int $eventId): Collection
    {
        return EventRsvp::where('event_id', $eventId)->get();
    }
}
