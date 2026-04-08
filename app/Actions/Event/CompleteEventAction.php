<?php

namespace App\Actions\Event;

use App\Enums\EventStatus;
use App\Models\Main\Event;
use App\Repositories\EventRepository;

class CompleteEventAction
{
    public function __construct(private EventRepository $repository) {}

    public function handle(Event $event): Event
    {
        return $this->repository->update($event, [
            'status' => EventStatus::Completed,
        ]);
    }
}
