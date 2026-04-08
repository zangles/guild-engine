<?php

namespace App\Finders;

use App\Models\Main\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogFinder
{
    public function findByGuild(int $guildId, int $perPage = 15): LengthAwarePaginator
    {
        return AuditLog::where('guild_id', $guildId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
