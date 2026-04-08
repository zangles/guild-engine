<?php

namespace App\Models\Main;

use App\Enums\RsvpResponse;
use Illuminate\Database\Eloquent\Model;

class EventRsvp extends Model
{
    protected $fillable = ['event_id', 'user_id', 'response', 'attended'];

    protected function casts(): array
    {
        return [
            'response' => RsvpResponse::class,
            'attended' => 'boolean',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
