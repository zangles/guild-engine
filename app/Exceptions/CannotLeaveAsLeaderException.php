<?php

namespace App\Exceptions;

use Exception;

class CannotLeaveAsLeaderException extends Exception
{
    public function __construct()
    {
        parent::__construct('The guild leader cannot leave without transferring leadership.', 422);
    }
}
