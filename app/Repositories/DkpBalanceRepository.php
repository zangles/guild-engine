<?php

namespace App\Repositories;

use App\Models\Main\DkpBalance;

class DkpBalanceRepository
{
    public function create(array $data): DkpBalance
    {
        return DkpBalance::create($data);
    }

    public function incrementBalance(DkpBalance $balance, int $amount): DkpBalance
    {
        $balance->increment('balance', $amount);
        return $balance->fresh();
    }

    public function decrementBalance(DkpBalance $balance, int $amount): DkpBalance
    {
        $balance->decrement('balance', $amount);
        return $balance->fresh();
    }
}
