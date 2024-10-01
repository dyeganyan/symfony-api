<?php

namespace App\Dto;

final class PriceCalculationResultDto
{
    public function __construct(
        public string $status,
        public float $finalPrice,
    ) {
    }
}