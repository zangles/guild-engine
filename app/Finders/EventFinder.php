<?php

namespace App\Finders;

use App\Enums\EventStatus;
use App\Models\Main\Event;
use Illuminate\Database\Eloquent\Collection;

class EventFinder
{
    public function findById(int $id): ?Event
    {
        return Event::find($id);
    }

    public function findByIdOrFail(int $id): Event
    {
        return Event::findOrFail($id);
    }

    public function findScheduledWithoutReminderSent(): Collection
    {
        return Event::where('status', EventStatus::Scheduled)
            ->whereNull('discord_reminder_sent_at')
            ->whereNotNull('discord_webhook_url')
            ->get();
    }
}
