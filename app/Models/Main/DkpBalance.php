<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class DkpBalance extends Model
{
    const CREATED_AT = null;

    protected $fillable = ['guild_id', 'user_id', 'balance'];

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
        ];
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
