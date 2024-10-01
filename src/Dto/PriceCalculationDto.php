<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Validator\Constraints\TaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class PriceCalculationDto
{
    #[ApiProperty(default: 1)]
    #[Assert\NotBlank()]
    #[Assert\Type(type: "integer")]
    public int $product;

    #[ApiProperty(default: "DE12131415")]
    #[Assert\NotBlank()]
    #[TaxNumber]
    public string $taxNumber;

    #[ApiProperty(default: "P10")]
    public ?string $couponCode;

    public function __construct(int $product, string $taxNumber, ?string $couponCode = null)
    {
        $this->product = $product;
        $this->taxNumber = $taxNumber;
        $this->couponCode = $couponCode;
    }
}
