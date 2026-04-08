<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class DkpTransaction extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'guild_id',
        'target_user_id',
        'actor_user_id',
        'amount',
        'balance_after',
        'reason',
    ];

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
