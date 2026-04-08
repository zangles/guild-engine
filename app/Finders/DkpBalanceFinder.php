<?php

namespace App\Finders;

use App\Models\Main\DkpBalance;

class DkpBalanceFinder
{
    public function findByGuildAndUser(int $guildId, int $userId): ?DkpBalance
    {
        return DkpBalance::where('guild_id', $guildId)
            ->where('user_id', $userId)
            ->first();
    }

    public function findOrCreateByGuildAndUser(int $guildId, int $userId): DkpBalance
    {
        return DkpBalance::firstOrCreate(
            ['guild_id' => $guildId, 'user_id' => $userId],
            ['balance' => 0]
        );
    }
}
