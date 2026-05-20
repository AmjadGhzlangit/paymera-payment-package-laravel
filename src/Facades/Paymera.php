<?php

namespace Casper\Paymera\Facades;

use Illuminate\Support\Facades\Facade;
use Casper\Paymera\DTOs\CreatePaymentRequest;
use Casper\Paymera\DTOs\CreatePaymentResult;
use Casper\Paymera\DTOs\PaymentStatusResult;

/**
 * @method static CreatePaymentResult createPayment(CreatePaymentRequest $request)
 * @method static PaymentStatusResult getPaymentStatus(string $paymentId)
 * @method static void cancelPayment(string $paymentId)
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
