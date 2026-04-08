<?php

namespace App\UseCases\DkpTransaction;

use App\DTO\DkpTransaction\DeductDkpDTO;
use App\Models\Main\DkpTransaction;
use App\Services\AuditLogService;
use App\Services\DkpBalanceService;
use App\Services\DkpTransactionService;

class DeductDkpProcess
{
    public function __construct(
        private DkpBalanceService $balanceService,
        private DkpTransactionService $transactionService,
        private AuditLogService $auditLogService,
    ) {}

    public function execute(DeductDkpDTO $dto): DkpTransaction
    {
        $updatedBalance = $this->balanceService->decrement($dto->guild_id, $dto->target_user_id, $dto->amount);

        $transaction = $this->transactionService->deduct($dto, $updatedBalance->balance);

        $this->auditLogService->log(
            $dto->guild_id,
            $dto->actor_user_id,
            $dto->target_user_id,
            'dkp.deducted',
            [
                'amount'        => $dto->amount,
                'reason'        => $dto->reason,
                'balance_after' => $updatedBalance->balance,
            ]
        );

        return $transaction;
    }
}
