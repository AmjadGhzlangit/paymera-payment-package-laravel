<?php

namespace Casper\Paymera;

use Illuminate\Support\Facades\Http;
use Casper\Paymera\DTOs\CreatePaymentRequest;
use Casper\Paymera\DTOs\CreatePaymentResult;
use Casper\Paymera\DTOs\PaymentStatusResult;
use Casper\Paymera\Exceptions\PaymeraException;
use Casper\Paymera\Exceptions\PaymentFailedException;
use Casper\Paymera\Exceptions\UnauthorizedException;

class PaymeraClient
{
    public function __construct(private readonly array $config)
    {
        if (! str_starts_with($this->config['base_url'] ?? '', 'https://')) {
            throw new PaymeraException(
                'Paymera base_url must use HTTPS; Basic Auth credentials would otherwise be sent in cleartext.',
                0,
            );
        }
    }

    private function handleErrorCode(int $code, string $message): void
    {
        match ($code) {
            0       => null,
            1       => throw new UnauthorizedException($message),
            100     => throw new PaymentFailedException($message),
            default => throw new PaymeraException($message, $code),
        };
    }

    private function parseResponse(mixed $body): array
    {
        if (!is_array($body) || !array_key_exists('ErrorCode', $body)) {
            throw new PaymeraException('Unexpected API response', 0);
        }

        return $body;
    }

    public function createPayment(CreatePaymentRequest $request): CreatePaymentResult
    {
        $response = Http::withBasicAuth($this->config['username'], $this->config['password'])
            ->connectTimeout(5)
            ->timeout(15)
            ->post($this->config['base_url'] . '/api/create-payment', $request->toArray());

        $body = $this->parseResponse($response->json());

        $this->handleErrorCode((int) $body['ErrorCode'], $body['ErrorMessage'] ?? 'Unknown error');

        return CreatePaymentResult::fromArray($body['Data']);
    }

    public function getPaymentStatus(string $paymentId): PaymentStatusResult
    {
        $response = Http::withBasicAuth($this->config['username'], $this->config['password'])
            ->connectTimeout(5)
            ->timeout(15)
            ->get($this->config['base_url'] . '/api/get-payment-status/' . $paymentId);

        $body = $this->parseResponse($response->json());

        $this->handleErrorCode((int) $body['ErrorCode'], $body['ErrorMessage'] ?? 'Unknown error');

        return PaymentStatusResult::fromArray($body['Data']);
    }

    public function cancelPayment(string $paymentId): void
    {
        $response = Http::withBasicAuth($this->config['username'], $this->config['password'])
            ->connectTimeout(5)
            ->timeout(15)
            ->post($this->config['base_url'] . '/api/cancel-payment', [
                'lang'       => $this->config['lang'],
                'payment_id' => $paymentId,
            ]);

        $body = $this->parseResponse($response->json());

        $this->handleErrorCode((int) $body['ErrorCode'], $body['ErrorMessage'] ?? 'Unknown error');
    }
}
