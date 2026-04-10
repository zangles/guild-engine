<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class GuildRole extends Model
{
    protected $fillable = ['guild_id', 'name', 'is_system', 'permissions'];

    protected function casts(): array
    {
        return [
            'is_system'   => 'boolean',
            'permissions' => 'array',
        ];
    }



}
