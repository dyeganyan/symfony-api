<?php

namespace App\Controller\Api;

use App\Dto\PurchaseDto;
use App\Service\PurchaseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class PurchaseController extends AbstractController
{
    public function __construct(
        private PurchaseService $purchaseService,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(Request $request)
    {
        $purchaseDto = $this->serializer->deserialize($request->getContent(), PurchaseDto::class, "json");

        $errors = $this->validator->validate($purchaseDto);
        if ($errors->count()) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->purchaseService->handlePurchase($purchaseDto);

        if ($result->status !== 'success') {
            return $this->json($result)->setStatusCode(400);
        }

        return $this->json($result);
    }
}
