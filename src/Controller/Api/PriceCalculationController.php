<?php

namespace App\Controller\Api;

use App\Dto\PriceCalculationDto;
use App\Service\PriceCalculationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class PriceCalculationController extends AbstractController
{
    public function __construct(
        private PriceCalculationService $priceCalculationService,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(Request $request)
    {
        $priceCalculationDto = $this->serializer->deserialize($request->getContent(), PriceCalculationDto::class, "json");
        $errors = $this->validator->validate($priceCalculationDto);
        if ($errors->count()) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->priceCalculationService->calculatePrice($priceCalculationDto);
        return $this->json($result);
    }
}
