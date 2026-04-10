<?php

namespace App\Models\Main;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'guild_id',
        'created_by_user_id',
        'title',
        'description',
        'starts_at',
        'max_attendees',
        'status',
        'discord_notified_creation',
        'discord_reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status'                    => EventStatus::class,
            'starts_at'                 => 'datetime',
            'discord_notified_creation' => 'boolean',
            'discord_reminder_sent_at'  => 'datetime',
        ];
    }


}
