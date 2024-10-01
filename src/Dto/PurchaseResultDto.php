<?php

namespace App\Dto;

class PurchaseResultDto
{
    public function __construct(
        public string $status,
        public string $transactionUuid,
        public string $paymentProcessor,
        public ?string $message = '',
    ) {}
}
