<?php

namespace App\Repositories;

use App\Models\Main\DkpTransaction;

class DkpTransactionRepository
{
    public function create(array $data): DkpTransaction
    {
        return DkpTransaction::create($data);
    }
}
