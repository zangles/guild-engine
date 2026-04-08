<?php

namespace App\Services;

use App\Actions\DkpTransaction\DeductDkpAction;
use App\Actions\DkpTransaction\GrantDkpAction;
use App\DTO\DkpTransaction\DeductDkpDTO;
use App\DTO\DkpTransaction\GrantDkpDTO;
use App\Models\Main\DkpTransaction;
use App\Queries\DkpTransactionQueries;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DkpTransactionService
{
    public function __construct(
        private DkpTransactionQueries $queries,
        private GrantDkpAction $grantAction,
        private DeductDkpAction $deductAction,
    ) {}

    public function getHistoryWithActors(int $guildId, int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->queries->getTransactionHistoryWithActors($guildId, $userId, $perPage);
    }

    public function grant(GrantDkpDTO $dto, int $balanceAfter): DkpTransaction
    {
        return $this->grantAction->handle($dto, $balanceAfter);
    }

    public function deduct(DeductDkpDTO $dto, int $balanceAfter): DkpTransaction
    {
        return $this->deductAction->handle($dto, $balanceAfter);
    }
}
