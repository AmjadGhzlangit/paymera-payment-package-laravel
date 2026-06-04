<?php

namespace Casper\Paymera\Facades;

use Illuminate\Support\Facades\Facade;
use Casper\Paymera\DTOs\CreatePaymentRequest;
use Casper\Paymera\DTOs\CreatePaymentResult;
use Casper\Paymera\DTOs\PaymentStatusResult;
use Casper\Paymera\Exceptions\PaymeraException;
use Casper\Paymera\Exceptions\PaymentFailedException;
use Casper\Paymera\Exceptions\UnauthorizedException;

/**
 * @method static CreatePaymentResult createPayment(CreatePaymentRequest $request) @throws UnauthorizedException|PaymentFailedException|PaymeraException
 * @method static PaymentStatusResult getPaymentStatus(string $paymentId) @throws UnauthorizedException|PaymentFailedException|PaymeraException
 * @method static void cancelPayment(string $paymentId) @throws UnauthorizedException|PaymentFailedException|PaymeraException
 *
 * @see \Casper\Paymera\PaymeraClient
 */
class Paymera extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'paymera';
    }
}
