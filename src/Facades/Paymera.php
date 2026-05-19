<?php

namespace YourVendor\Paymera\Facades;

use Illuminate\Support\Facades\Facade;
use YourVendor\Paymera\DTOs\CreatePaymentRequest;
use YourVendor\Paymera\DTOs\CreatePaymentResult;
use YourVendor\Paymera\DTOs\PaymentStatusResult;

/**
 * @method static CreatePaymentResult createPayment(CreatePaymentRequest $request)
 * @method static PaymentStatusResult getPaymentStatus(string $paymentId)
 * @method static void cancelPayment(string $paymentId)
 *
 * @see \YourVendor\Paymera\PaymeraClient
 */
class Paymera extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'paymera';
    }
}
