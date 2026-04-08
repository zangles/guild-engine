<?php

namespace App\Models\Main;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function guildMembers()
    {
        return $this->hasMany(GuildMember::class);
    }

    public function receivedDkp()
    {
        return $this->hasMany(DkpTransaction::class, 'target_user_id');
    }

    public function grantedDkp()
    {
        return $this->hasMany(DkpTransaction::class, 'actor_user_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }

    public function eventRsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }
}
