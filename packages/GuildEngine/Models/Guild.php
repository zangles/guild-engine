<?php

namespace GuildEngine\Models;

use GuildEngine\Database\Factories\GuildFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $user_id
 */
final class Guild extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    protected static function newFactory(): GuildFactory
    {
        return GuildFactory::new();
    }
}
