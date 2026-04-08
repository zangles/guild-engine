<?php

namespace App\DTO\Event;

use Carbon\Carbon;

readonly class CreateEventDTO
{
    public function __construct(
        public int $guild_id,
        public int $created_by_user_id,
        public string $title,
        public ?string $description,
        public Carbon $starts_at,
        public ?int $max_attendees,
    ) {}
}
