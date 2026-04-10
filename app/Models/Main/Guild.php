<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    protected $fillable = [
        'name',
        'description',
        'game',
        'is_public',
        'leader_user_id',
        'dkp_currency_name',
        'discord_webhook_url',
        'discord_advance_notice_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }


}
