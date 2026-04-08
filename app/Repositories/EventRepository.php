<?php

namespace App\Repositories;

use App\Models\Main\Event;

class EventRepository
{
    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($data);
        return $event->fresh();
    }
}
