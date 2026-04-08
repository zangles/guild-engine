<?php

namespace App\Actions\DkpTransaction;

use App\DTO\DkpTransaction\DeductDkpDTO;
use App\Models\Main\DkpTransaction;
use App\Repositories\DkpTransactionRepository;

class DeductDkpAction
{
    public function __construct(private DkpTransactionRepository $repository) {}

    public function handle(DeductDkpDTO $dto, int $balanceAfter): DkpTransaction
    {
        return $this->repository->create([
            'guild_id'       => $dto->guild_id,
            'target_user_id' => $dto->target_user_id,
            'actor_user_id'  => $dto->actor_user_id,
            'amount'         => -abs($dto->amount),
            'balance_after'  => $balanceAfter,
            'reason'         => $dto->reason,
        ]);
    }
}
