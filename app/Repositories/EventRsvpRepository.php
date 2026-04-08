<?php

namespace App\Repositories;

use App\Models\Main\EventRsvp;

class EventRsvpRepository
{
    public function create(array $data): EventRsvp
    {
        return EventRsvp::create($data);
    }

    public function update(EventRsvp $rsvp, array $data): EventRsvp
    {
        $rsvp->update($data);
        return $rsvp->fresh();
    }
}
