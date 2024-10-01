<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Post;
use App\Dto\PriceCalculationDto;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\PriceCalculationResultDto;
use App\Controller\Api\PriceCalculationController;

#[ApiResource(
    operations: [
        new Post(
            name: 'PriceCalculation',
            uriTemplate: '/calculate-price',
            controller: PriceCalculationController::class,
            input: PriceCalculationDto::class,
            output: PriceCalculationResultDto::class,
        )
    ]
)]
class PriceCalculationApi
{
}
