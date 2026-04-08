<?php

namespace App\Exceptions;

use Exception;

class InsufficientPermissionsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Insufficient permissions.', 403);
    }
}
