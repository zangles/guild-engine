<?php

namespace App\Queries;

use App\Models\Main\DkpTransaction;
use App\Models\Main\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DkpTransactionQueries
{
    public function getTransactionHistoryWithActors(int $guildId, int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $transactions = DkpTransaction::where('guild_id', $guildId)
            ->where('target_user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $actorIds = $transactions->pluck('actor_user_id')->unique()->filter()->values()->toArray();
        $users = User::whereIn('id', $actorIds)->get()->keyBy('id');

        foreach ($transactions as $transaction) {
            $transaction->setRelation('actor', $users[$transaction->actor_user_id] ?? null);
        }

        return $transactions;
    }
}
