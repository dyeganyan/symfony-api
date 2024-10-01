<?php

namespace App\Payment;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalPaymentProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
        private PaypalPaymentProcessor $paypalPaymentProcessor
    ) {
    }

    public function process(float $price): bool
    {
        $this->paypalPaymentProcessor->pay((int) ($price * 100));
        return true;
    }
}
