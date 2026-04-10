<?php

namespace App\Models\Main;

use App\Enums\DonationStatus;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'guild_id',
        'user_id',
        'amount',
        'note',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => DonationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }


}
