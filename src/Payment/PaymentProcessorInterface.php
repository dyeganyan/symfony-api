<?php

namespace App\Payment;

interface PaymentProcessorInterface
{
    /**
     * Process the payment.
     *
     * @param float $price Payment amount
     * @throws \Exception if payment fails
     */
    public function process(float $price): bool;
}
