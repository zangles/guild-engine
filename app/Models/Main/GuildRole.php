<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class GuildRole extends Model
{
    protected $fillable = ['guild_id', 'name', 'is_system'];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function members()
    {
        return $this->hasMany(GuildMember::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'guild_role_permissions');
    }
}
