<?php

use Illuminate\Support\Facades\Http;
use YourVendor\Paymera\DTOs\CreatePaymentRequest;
use YourVendor\Paymera\DTOs\CreatePaymentResult;
use YourVendor\Paymera\Exceptions\PaymentFailedException;
use YourVendor\Paymera\Exceptions\UnauthorizedException;
use YourVendor\Paymera\PaymeraClient;

beforeEach(function () {
    $this->config = [
        'base_url'    => 'https://egate-t.paymera.cc',
        'username'    => 'test-user',
        'password'    => 'test-pass',
        'terminal_id' => 'TERM001',
        'lang'        => 'en',
    ];

    $this->client = new PaymeraClient($this->config);

    $this->request = new CreatePaymentRequest(
        amount:      1000,
        callbackURL: 'https://example.com/callback',
        triggerURL:  'https://example.com/trigger',
        terminalId:  'TERM001',
        lang:        'en',
    );
});

it('creates payment and returns CreatePaymentResult on ErrorCode 0', function () {
    Http::fake([
        '*/api/create-payment' => Http::response([
            'ErrorCode'    => 0,
            'ErrorMessage' => 'Success',
            'Data'         => [
                'paymentId' => 'pay_abc123',
                'url'       => 'https://egate-t.paymera.cc/pay/pay_abc123',
            ],
        ]),
    ]);

    $result = $this->client->createPayment($this->request);

    expect($result)->toBeInstanceOf(CreatePaymentResult::class)
        ->and($result->paymentId)->toBe('pay_abc123')
        ->and($result->redirectUrl)->toBe('https://egate-t.paymera.cc/pay/pay_abc123');
});

it('throws UnauthorizedException on ErrorCode 1', function () {
    Http::fake([
        '*/api/create-payment' => Http::response([
            'ErrorCode'    => 1,
            'ErrorMessage' => 'Unauthorized',
            'Data'         => null,
        ]),
    ]);

    $this->client->createPayment($this->request);
})->throws(UnauthorizedException::class);

it('throws PaymentFailedException on ErrorCode 100', function () {
    Http::fake([
        '*/api/create-payment' => Http::response([
            'ErrorCode'    => 100,
            'ErrorMessage' => 'Payment failed',
            'Data'         => null,
        ]),
    ]);

    $this->client->createPayment($this->request);
})->throws(PaymentFailedException::class);
