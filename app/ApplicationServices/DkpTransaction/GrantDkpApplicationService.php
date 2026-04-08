<?php

namespace App\ApplicationServices\DkpTransaction;

use App\DTO\DkpTransaction\GrantDkpDTO;
use App\Models\Main\DkpTransaction;
use App\UseCases\DkpTransaction\GrantDkpProcess;
use Illuminate\Support\Facades\DB;

class GrantDkpApplicationService
{
    public function __construct(private GrantDkpProcess $process) {}

    public function handle(GrantDkpDTO $dto): DkpTransaction
    {
        return DB::transaction(fn () => $this->process->execute($dto));
    }
}
