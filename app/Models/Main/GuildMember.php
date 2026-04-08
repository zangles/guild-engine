<?php

namespace App\Models\Main;

use App\Enums\GuildMemberStatus;
use Illuminate\Database\Eloquent\Model;

class GuildMember extends Model
{
    protected $fillable = [
        'guild_id',
        'user_id',
        'guild_role_id',
        'status',
        'invited_by_user_id',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'status'    => GuildMemberStatus::class,
            'joined_at' => 'datetime',
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

    public function role()
    {
        return $this->belongsTo(GuildRole::class, 'guild_role_id');
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }
}
