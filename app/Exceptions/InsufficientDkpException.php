<?php

namespace App\Exceptions;

use Exception;

class InsufficientDkpException extends Exception
{
    public function __construct()
    {
        parent::__construct('Insufficient DKP balance.', 422);
    }
}
