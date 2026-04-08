<?php

namespace App\ApplicationServices\DkpTransaction;

use App\DTO\DkpTransaction\DeductDkpDTO;
use App\Exceptions\InsufficientDkpException;
use App\Models\Main\DkpTransaction;
use App\UseCases\DkpTransaction\DeductDkpProcess;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeductDkpApplicationService
{
    public function __construct(private DeductDkpProcess $process) {}

    public function handle(DeductDkpDTO $dto): DkpTransaction
    {
        return DB::transaction(fn () => $this->process->execute($dto));
    }
}
