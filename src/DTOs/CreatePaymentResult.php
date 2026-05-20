<?php

namespace Casper\Paymera\DTOs;

readonly class CreatePaymentResult
{
    private function __construct(
        public string $paymentId,
        public string $redirectUrl,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            paymentId:   $data['paymentId'],
            redirectUrl: $data['url'],
        );
    }
}
