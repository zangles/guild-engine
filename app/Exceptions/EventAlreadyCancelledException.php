<?php

namespace App\Exceptions;

use Exception;

class EventAlreadyCancelledException extends Exception
{
    public function __construct()
    {
        parent::__construct('This event has already been cancelled.', 422);
    }
}
