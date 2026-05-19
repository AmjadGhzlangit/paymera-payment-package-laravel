<?php

namespace YourVendor\Paymera;

use Illuminate\Support\Facades\Http;
use YourVendor\Paymera\DTOs\CreatePaymentRequest;
use YourVendor\Paymera\DTOs\CreatePaymentResult;
use YourVendor\Paymera\DTOs\PaymentStatusResult;
use YourVendor\Paymera\Exceptions\PaymeraException;
use YourVendor\Paymera\Exceptions\PaymentFailedException;
use YourVendor\Paymera\Exceptions\UnauthorizedException;

class PaymeraClient
{
    public function __construct(private readonly array $config) {}

    private function handleErrorCode(int $code, string $message): void
    {
        match ($code) {
            0       => null,
            1       => throw new UnauthorizedException($message),
            100     => throw new PaymentFailedException($message),
            default => throw new PaymeraException($message, $code),
        };
    }

    public function createPayment(CreatePaymentRequest $request): CreatePaymentResult
    {
        $response = Http::withBasicAuth($this->config['username'], $this->config['password'])
            ->post($this->config['base_url'] . '/api/create-payment', $request->toArray());

        $body = $response->json();

        $this->handleErrorCode((int) $body['ErrorCode'], $body['ErrorMessage'] ?? 'Unknown error');

        return CreatePaymentResult::fromArray($body['Data']);
    }

    public function getPaymentStatus(string $paymentId): PaymentStatusResult
    {
        $response = Http::withBasicAuth($this->config['username'], $this->config['password'])
            ->get($this->config['base_url'] . '/api/get-payment-status/' . $paymentId);

        $body = $response->json();

        $this->handleErrorCode((int) $body['ErrorCode'], $body['ErrorMessage'] ?? 'Unknown error');

        return PaymentStatusResult::fromArray($body['Data']);
    }

    public function cancelPayment(string $paymentId): void
    {
        $response = Http::withBasicAuth($this->config['username'], $this->config['password'])
            ->post($this->config['base_url'] . '/api/cancel-payment', [
                'lang'       => $this->config['lang'],
                'payment_id' => $paymentId,
            ]);

        $body = $response->json();

        $this->handleErrorCode((int) $body['ErrorCode'], $body['ErrorMessage'] ?? 'Unknown error');
    }
}
