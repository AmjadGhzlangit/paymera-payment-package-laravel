<?php

namespace Casper\Paymera\DTOs;

use Casper\Paymera\Enums\PaymentStatus;

readonly class PaymentStatusResult
{
    private function __construct(
        public PaymentStatus $status,
        public string $rrn,
        public int $amount,
        public string $terminalId,
        public string $creationTimestamp,
        public string|null $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status:            PaymentStatus::from($data['status']),
            rrn:               $data['rrn'],
            amount:            (int) $data['amount'],
            terminalId:        $data['terminalId'],
            creationTimestamp: $data['creationTimestamp'],
            notes:             $data['notes'] ?? null,
        );
    }

    public function isAccepted(): bool
    {
        return $this->status === PaymentStatus::Accepted;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::Failed;
    }

    public function isCanceled(): bool
    {
        return $this->status === PaymentStatus::Canceled;
    }
}
