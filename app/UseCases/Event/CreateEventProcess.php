<?php

namespace App\UseCases\Event;

use App\DTO\Event\CreateEventDTO;
use App\Models\Main\Event;
use App\Services\EventService;

class CreateEventProcess
{
    public function __construct(private EventService $eventService) {}

    public function execute(CreateEventDTO $dto): Event
    {
        return $this->eventService->create($dto);
    }
}
