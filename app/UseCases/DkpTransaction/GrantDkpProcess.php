<?php

namespace App\UseCases\DkpTransaction;

use App\DTO\DkpTransaction\GrantDkpDTO;
use App\Models\Main\DkpTransaction;
use App\Services\AuditLogService;
use App\Services\DkpBalanceService;
use App\Services\DkpTransactionService;

class GrantDkpProcess
{
    public function __construct(
        private DkpBalanceService $balanceService,
        private DkpTransactionService $transactionService,
        private AuditLogService $auditLogService,
    ) {}

    public function execute(GrantDkpDTO $dto): DkpTransaction
    {
        $balance = $this->balanceService->getOrCreate($dto->guild_id, $dto->target_user_id);
        $updatedBalance = $this->balanceService->increment($dto->guild_id, $dto->target_user_id, $dto->amount);

        $transaction = $this->transactionService->grant($dto, $updatedBalance->balance);

        $this->auditLogService->log(
            $dto->guild_id,
            $dto->actor_user_id,
            $dto->target_user_id,
            'dkp.granted',
            [
                'amount'        => $dto->amount,
                'reason'        => $dto->reason,
                'balance_after' => $updatedBalance->balance,
            ]
        );

        return $transaction;
    }
}
