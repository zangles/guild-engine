<?php

namespace App\DTO\Event;

readonly class RegisterAttendanceDTO
{
    public function __construct(
        public int $event_id,
        public array $attendances, // [['user_id' => int, 'attended' => bool]]
    ) {}
}
