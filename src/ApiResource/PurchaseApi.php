<?php

namespace App\ApiResource;

use App\Dto\PurchaseDto;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\PriceCalculationResultDto;
use App\Controller\Api\PurchaseController;

#[ApiResource(
    operations: [
        new Post(
            name: 'Purchase',
            uriTemplate: '/purchase',
            controller: PurchaseController::class,
            input: PurchaseDto::class,
            output: PriceCalculationResultDto::class
        )
    ]
)]
class PurchaseApi
{
}
