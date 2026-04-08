<?php

namespace App\Actions\EventRsvp;

use App\DTO\Event\RegisterAttendanceDTO;
use App\Models\Main\EventRsvp;

class RegisterAttendanceAction
{
    public function handle(RegisterAttendanceDTO $dto): void
    {
        foreach ($dto->attendances as $attendance) {
            EventRsvp::where('event_id', $dto->event_id)
                ->where('user_id', $attendance['user_id'])
                ->update(['attended' => $attendance['attended']]);
        }
    }
}
