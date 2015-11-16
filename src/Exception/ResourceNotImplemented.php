<?php

namespace Snorlax\Exception;

use InvalidArgumentException;

class ResourceNotImplemented extends InvalidArgumentException
{
    const MESSAGE_TPL = 'Resource "%s" is not implemented';

    public function __construct($resource_name)
    {
        $message = sprintf(self::MESSAGE_TPL, $resource_name);

        parent::__construct($message);
    }
}
