<?php

namespace App\Exceptions;

use Exception;

class MemberAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('User is already a member of this guild.', 409);
    }
}
