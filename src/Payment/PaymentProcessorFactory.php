<?php

namespace App\Payment;
use App\Enum\PaymentMethod;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorFactory
{
    public function getPaymentProcessor(string $paymentProcessor): PaymentProcessorInterface
    {
        return match ($paymentProcessor) {
            PaymentMethod::PAYPAL->value => new PaypalPaymentProcessorAdapter(new PaypalPaymentProcessor()),
            PaymentMethod::STRIPE->value => new StripePaymentProcessorAdapter(new StripePaymentProcessor()),
            default => throw new \InvalidArgumentException("Invalid payment processor: $paymentProcessor"),
        };
    }
}
