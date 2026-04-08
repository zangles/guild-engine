<?php

namespace App\Services;

use App\Actions\DkpBalance\UpdateDkpBalanceAction;
use App\Exceptions\InsufficientDkpException;
use App\Finders\DkpBalanceFinder;
use App\Models\Main\DkpBalance;

class DkpBalanceService
{
    public function __construct(
        private DkpBalanceFinder $finder,
        private UpdateDkpBalanceAction $updateAction,
    ) {}

    public function getBalance(int $guildId, int $userId): DkpBalance
    {
        return $this->finder->findOrCreateByGuildAndUser($guildId, $userId);
    }

    public function getOrCreate(int $guildId, int $userId): DkpBalance
    {
        return $this->finder->findOrCreateByGuildAndUser($guildId, $userId);
    }

    public function increment(int $guildId, int $userId, int $amount): DkpBalance
    {
        $balance = $this->finder->findOrCreateByGuildAndUser($guildId, $userId);
        return $this->updateAction->increment($balance, $amount);
    }

    public function decrement(int $guildId, int $userId, int $amount): DkpBalance
    {
        $balance = $this->finder->findOrCreateByGuildAndUser($guildId, $userId);

        if ($balance->balance < $amount) {
            throw new InsufficientDkpException();
        }

        return $this->updateAction->decrement($balance, $amount);
    }
}
