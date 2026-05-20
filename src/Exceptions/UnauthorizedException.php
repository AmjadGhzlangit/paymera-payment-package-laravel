<?php

namespace Casper\Paymera\Exceptions;

class UnauthorizedException extends PaymeraException
{
    public function __construct(string $message = 'Unauthorized', ?\Throwable $previous = null)
    {
        parent::__construct($message, 1, $previous);
    }
}
