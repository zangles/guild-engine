<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['slug', 'name'];

    public function roles()
    {
        return $this->belongsToMany(GuildRole::class, 'guild_role_permissions');
    }
}
