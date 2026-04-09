<?php

namespace App\Models\Main;

use App\Enums\GuildPermission;
use App\Enums\SystemRole;
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

    public function hasPermission(GuildPermission $permission): bool
    {
        if ($this->is_system) {
            return in_array($permission, SystemRole::from($this->name)->permissions());
        }

        return in_array($permission->value, $this->permissions ?? []);
    }

    public function getPermissionSlugs(): array
    {
        if ($this->is_system) {
            return array_map(fn ($p) => $p->value, SystemRole::from($this->name)->permissions());
        }

        return $this->permissions ?? [];
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function members()
    {
        return $this->hasMany(GuildMember::class);
    }
}
