<?php

namespace App\Exceptions;

use Exception;

class DonationNotPendingException extends Exception
{
    public function __construct()
    {
        parent::__construct('This donation is not in pending status.', 409);
    }
}
