<?php

namespace App\ApplicationServices\Event;

use App\DTO\Event\CreateEventDTO;
use App\Jobs\SendDiscordEventCreatedNotificationJob;
use App\Jobs\SendDiscordEventReminderJob;
use App\Models\Main\Event;
use App\Models\Main\Guild;
use App\UseCases\Event\CreateEventProcess;
use Illuminate\Support\Facades\DB;

class CreateEventApplicationService
{
    public function __construct(private CreateEventProcess $process) {}

    public function handle(CreateEventDTO $dto, Guild $guild): Event
    {
        $event = DB::transaction(fn () => $this->process->execute($dto));

        if ($guild->discord_webhook_url) {
            SendDiscordEventCreatedNotificationJob::dispatch($event->id, $guild->discord_webhook_url);

            if ($guild->discord_advance_notice_minutes) {
                $reminderAt = $event->starts_at->subMinutes($guild->discord_advance_notice_minutes);
                SendDiscordEventReminderJob::dispatch($event->id, $guild->discord_webhook_url)->delay($reminderAt);
            }
        }

        return $event;
    }
}
