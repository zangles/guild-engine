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

    public function members()
    {
        return $this->hasMany(GuildMember::class);
    }

    public function roles()
    {
        return $this->hasMany(GuildRole::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function dkpTransactions()
    {
        return $this->hasMany(DkpTransaction::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }
}
