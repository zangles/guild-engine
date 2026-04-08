<?php

namespace App\Exceptions;

use Exception;

class CannotKickLeaderException extends Exception
{
    public function __construct()
    {
        parent::__construct('Cannot kick the guild leader.', 422);
    }
}
