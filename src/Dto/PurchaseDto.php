<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Validator\Constraints\TaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseDto
{
    #[ApiProperty(default: 1)]
    #[Assert\NotBlank]
    public ?int $product;

    #[ApiProperty(default: "DE12131415")]
    #[Assert\NotBlank]
    #[TaxNumber]
    public ?string $taxNumber;

    #[ApiProperty(default: "P10")]
    public ?string $couponCode = null;

    #[Assert\NotBlank]
    // TODO: use from enum
    #[Assert\Choice(choices: ["paypal", "stripe"], message: "Choose a valid payment processor")]
    public ?string $paymentProcessor;

    public function __construct(
        ?int $product = null,
        ?string $taxNumber = null,
        ?string $couponCode = null,
        ?string $paymentProcessor = null
    ) {
        $this->product = $product;
        $this->taxNumber = $taxNumber;
        $this->couponCode = $couponCode;
        $this->paymentProcessor = $paymentProcessor;
    }
}
