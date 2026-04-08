<?php

namespace App\Actions\Event;

use App\DTO\Event\CreateEventDTO;
use App\Enums\EventStatus;
use App\Models\Main\Event;
use App\Repositories\EventRepository;

class CreateEventAction
{
    public function __construct(private EventRepository $repository) {}

    public function handle(CreateEventDTO $dto): Event
    {
        return $this->repository->create([
            'guild_id'            => $dto->guild_id,
            'created_by_user_id'  => $dto->created_by_user_id,
            'title'               => $dto->title,
            'description'         => $dto->description,
            'starts_at'           => $dto->starts_at,
            'max_attendees'       => $dto->max_attendees,
            'status'              => EventStatus::Scheduled,
        ]);
    }
}
