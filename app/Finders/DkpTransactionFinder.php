<?php

namespace App\Finders;

use App\Models\Main\DkpTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DkpTransactionFinder
{
    public function findByGuildAndUser(int $guildId, int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return DkpTransaction::where('guild_id', $guildId)
            ->where('target_user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
