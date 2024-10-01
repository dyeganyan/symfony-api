<?php
namespace App\Payment;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripePaymentProcessorAdapter implements PaymentProcessorInterface
{

    public function __construct(private StripePaymentProcessor $stripePaymentProcessor)
    {
    }

    public function process(float $price): bool
    {
        return $this->stripePaymentProcessor->processPayment(($price * 100));
    }
}
