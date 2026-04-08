<?php

namespace App\Queries;

use App\Models\Main\DkpTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DkpTransactionQueries
{
    public function getTransactionHistoryWithActors(int $guildId, int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return DkpTransaction::with(['actor', 'target'])
            ->where('guild_id', $guildId)
            ->where('target_user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
