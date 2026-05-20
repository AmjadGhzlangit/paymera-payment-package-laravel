<?php

use Illuminate\Support\Facades\Http;
use Casper\Paymera\DTOs\PaymentStatusResult;
use Casper\Paymera\Enums\PaymentStatus;
use Casper\Paymera\Exceptions\PaymentFailedException;
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

it('returns PaymentStatusResult with correct status enum on success', function () {
    Http::fake([
        '*/api/get-payment-status/*' => Http::response([
            'ErrorCode'    => 0,
            'ErrorMessage' => 'Success',
            'Data'         => [
                'status'            => 'A',
                'rrn'               => 'RRN123456',
                'amount'            => 1000,
                'terminalId'        => 'TERM001',
                'creationTimestamp' => '2026-01-01T12:00:00Z',
                'notes'             => null,
            ],
        ]),
    ]);

    $result = $this->client->getPaymentStatus('pay_abc123');

    expect($result)->toBeInstanceOf(PaymentStatusResult::class)
        ->and($result->status)->toBe(PaymentStatus::Accepted)
        ->and($result->rrn)->toBe('RRN123456')
        ->and($result->amount)->toBe(1000);
});

it('returns isAccepted() true when status is A', function () {
    Http::fake([
        '*/api/get-payment-status/*' => Http::response([
            'ErrorCode'    => 0,
            'ErrorMessage' => 'Success',
            'Data'         => [
                'status'            => 'A',
                'rrn'               => 'RRN123456',
                'amount'            => 1000,
                'terminalId'        => 'TERM001',
                'creationTimestamp' => '2026-01-01T12:00:00Z',
                'notes'             => null,
            ],
        ]),
    ]);

    $result = $this->client->getPaymentStatus('pay_abc123');

    expect($result->isAccepted())->toBeTrue()
        ->and($result->isPending())->toBeFalse()
        ->and($result->isFailed())->toBeFalse()
        ->and($result->isCanceled())->toBeFalse();
});

it('returns isPending() true when status is P', function () {
    Http::fake([
        '*/api/get-payment-status/*' => Http::response([
            'ErrorCode'    => 0,
            'ErrorMessage' => 'Success',
            'Data'         => [
                'status'            => 'P',
                'rrn'               => 'RRN123456',
                'amount'            => 1000,
                'terminalId'        => 'TERM001',
                'creationTimestamp' => '2026-01-01T12:00:00Z',
                'notes'             => null,
            ],
        ]),
    ]);

    $result = $this->client->getPaymentStatus('pay_abc123');

    expect($result->isPending())->toBeTrue()
        ->and($result->isAccepted())->toBeFalse();
});

it('throws on ErrorCode 100', function () {
    Http::fake([
        '*/api/get-payment-status/*' => Http::response([
            'ErrorCode'    => 100,
            'ErrorMessage' => 'Payment failed',
            'Data'         => null,
        ]),
    ]);

    $this->client->getPaymentStatus('pay_abc123');
})->throws(PaymentFailedException::class);
