<?php

use Illuminate\Support\Facades\Http;
use Casper\Paymera\Exceptions\UnauthorizedException;
use Casper\Paymera\PaymeraClient;

beforeEach(function () {
    $this->config = [
        'base_url'    => 'https://egate-t.paymera.cc',
        'username'    => 'test-user',
        'password'    => 'test-pass',
        'terminal_id' => 'TERM001',
        'lang'        => 'en',
    ];

    $this->client = new PaymeraClient($this->config);
});

it('cancels payment successfully on ErrorCode 0', function () {
    Http::fake([
        '*/api/cancel-payment' => Http::response([
            'ErrorCode'    => 0,
            'ErrorMessage' => 'Success',
            'Data'         => null,
        ]),
    ]);

    $this->client->cancelPayment('pay_abc123');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/api/cancel-payment')
            && $request['payment_id'] === 'pay_abc123'
            && $request['lang'] === 'en';
    });
});

it('throws UnauthorizedException on ErrorCode 1', function () {
    Http::fake([
        '*/api/cancel-payment' => Http::response([
            'ErrorCode'    => 1,
            'ErrorMessage' => 'Unauthorized',
            'Data'         => null,
        ]),
    ]);

    $this->client->cancelPayment('pay_abc123');
})->throws(UnauthorizedException::class);
