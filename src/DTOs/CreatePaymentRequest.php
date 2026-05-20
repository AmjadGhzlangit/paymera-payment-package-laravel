<?php

namespace Casper\Paymera\DTOs;

readonly class CreatePaymentRequest
{
    public function __construct(
        public int $amount,
        public string $callbackURL,
        public string $triggerURL,
        public string $terminalId,
        public string $lang,
        public string|null $notes = null,
        public int $savedCards = 0,
        public string|null $appUser = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'amount'      => $this->amount,
            'callbackURL' => $this->callbackURL,
            'triggerURL'  => $this->triggerURL,
            'terminalId'  => $this->terminalId,
            'lang'        => $this->lang,
            'savedCards'  => $this->savedCards,
        ];

        if ($this->notes !== null) {
            $data['notes'] = $this->notes;
        }

        if ($this->appUser !== null) {
            $data['appUser'] = $this->appUser;
        }

        return $data;
    }
}
