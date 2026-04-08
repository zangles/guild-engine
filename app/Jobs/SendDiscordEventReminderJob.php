<?php

namespace App\Jobs;

use App\Enums\EventStatus;
use App\Models\Main\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class SendDiscordEventReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $eventId,
        private string $webhookUrl,
    ) {}

    public function handle(): void
    {
        $event = Event::find($this->eventId);

        if (!$event || $event->status !== EventStatus::Scheduled) {
            return;
        }

        Http::post($this->webhookUrl, [
            'content' => "⏰ Recordatorio: el evento **{$event->title}** comienza en breve ({$event->starts_at->format('d/m/Y H:i')})",
        ]);

        $event->update(['discord_reminder_sent_at' => Carbon::now()]);
    }
}
