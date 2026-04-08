<?php

namespace App\Actions\EventRsvp;

use App\DTO\EventRsvp\UpsertRsvpDTO;
use App\Models\Main\EventRsvp;
use App\Repositories\EventRsvpRepository;

class UpsertEventRsvpAction
{
    public function __construct(
        private EventRsvpRepository $repository,
    ) {}

    public function handle(UpsertRsvpDTO $dto): EventRsvp
    {
        $existing = EventRsvp::where('event_id', $dto->event_id)
            ->where('user_id', $dto->user_id)
            ->first();

        if ($existing) {
            return $this->repository->update($existing, ['response' => $dto->response]);
        }

        return $this->repository->create([
            'event_id' => $dto->event_id,
            'user_id'  => $dto->user_id,
            'response' => $dto->response,
        ]);
    }
}
