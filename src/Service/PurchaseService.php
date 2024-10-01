<?php

namespace App\Service;

use App\Dto\PriceCalculationDto;
use App\Dto\PurchaseDto;
use App\Dto\PurchaseResultDto;
use App\Payment\PaymentProcessorFactory;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use Psr\Log\LoggerInterface;

class PurchaseService
{

    public function __construct(
        private ProductRepository $productRepository,
        private CouponRepository $couponRepository,
        private LoggerInterface $logger,
        private PaymentProcessorFactory $paymentProcessorFactory,
        private PriceCalculationService $priceCalculationService
    ) {

    }

    public function handlePurchase(PurchaseDto $purchaseDto): PurchaseResultDto
    {
        $priceCalculationDto = new PriceCalculationDto(
            product: $purchaseDto->product,
            taxNumber: $purchaseDto->taxNumber,
            couponCode: $purchaseDto->couponCode
        );

        $finalPriceRes = $this->priceCalculationService->calculatePrice(priceCalculationDto: $priceCalculationDto);

        $paymentProcessor = $this->paymentProcessorFactory->getPaymentProcessor(paymentProcessor: $purchaseDto->paymentProcessor);

        try {
            $paymentResult = $paymentProcessor->process($finalPriceRes->finalPrice);
            if (!$paymentResult) {
                throw new \Exception('Payment process failed');
            }
        } catch (\Exception $exception) {
            return new PurchaseResultDto(
                status: 'error',
                transactionUuid: 'uuid-format-like-string',
                paymentProcessor: $purchaseDto->paymentProcessor,
                message: $exception->getMessage()
            );
        }

        return new PurchaseResultDto(
            status: 'success',
            transactionUuid: 'uuid-format-like-string',
            paymentProcessor: $purchaseDto->paymentProcessor,
            message: 'Payment successfully made'
        );
    }
}
