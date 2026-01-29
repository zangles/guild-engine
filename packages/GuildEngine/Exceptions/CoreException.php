<?php

namespace GuildEngine\Exceptions;

use Exception;

abstract class CoreException extends Exception
{
    final public function getErrorCode(): string
    {
        return static::ERROR_CODE;
    }
}
