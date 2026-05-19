<?php

namespace YourVendor\Paymera\Exceptions;

class PaymentFailedException extends PaymeraException
{
    public function __construct(string $message = 'Payment failed', ?\Throwable $previous = null)
    {
        parent::__construct($message, 100, $previous);
    }
}
