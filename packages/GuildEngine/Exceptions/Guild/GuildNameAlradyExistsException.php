<?php

namespace GuildEngine\Exceptions\Guild;

use Exception;
use GuildEngine\Exceptions\CoreException;

class GuildNameAlradyExistsException extends CoreException
{
    public const ERROR_CODE = 'GUILD_NAME_EXISTS';

    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
