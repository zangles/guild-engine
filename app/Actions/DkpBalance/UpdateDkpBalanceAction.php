<?php

namespace App\Actions\DkpBalance;

use App\Models\Main\DkpBalance;
use App\Repositories\DkpBalanceRepository;

class UpdateDkpBalanceAction
{
    public function __construct(private DkpBalanceRepository $repository) {}

    public function increment(DkpBalance $balance, int $amount): DkpBalance
    {
        return $this->repository->incrementBalance($balance, $amount);
    }

    public function decrement(DkpBalance $balance, int $amount): DkpBalance
    {
        return $this->repository->decrementBalance($balance, $amount);
    }
}
