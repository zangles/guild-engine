<?php

namespace App\DTO\EventRsvp;

readonly class UpsertRsvpDTO
{
    public function __construct(
        public int $event_id,
        public int $user_id,
        public string $response,
    ) {}
}
