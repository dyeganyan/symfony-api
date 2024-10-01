<?php

namespace App\Tests\Service;

use App\Dto\PriceCalculationDto;
use App\Dto\PriceCalculationResultDto;
use App\Dto\PurchaseDto;
use App\Dto\PurchaseResultDto;
use App\Enum\PaymentMethod;
use App\Payment\PaymentProcessorFactory;
use App\Payment\PaypalPaymentProcessorAdapter;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\PriceCalculationService;
use App\Service\PurchaseService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PurchaseServiceTest extends TestCase
{
    private ProductRepository $productRepository;
    private CouponRepository $couponRepository;
    private LoggerInterface $logger;
    private PaymentProcessorFactory $paymentProcessorFactory;
    private PriceCalculationService $priceCalculationService;
    private PurchaseService $purchaseService;

    protected function setUp(): void
    {
        // Create mock objects for dependencies
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->couponRepository = $this->createMock(CouponRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->paymentProcessorFactory = $this->createMock(PaymentProcessorFactory::class);
        $this->priceCalculationService = $this->createMock(PriceCalculationService::class);

        // Initialize the service
        $this->purchaseService = new PurchaseService(
            $this->productRepository,
            $this->couponRepository,
            $this->logger,
            $this->paymentProcessorFactory,
            $this->priceCalculationService
        );
    }

    public function testHandlePurchaseSuccessful(): void
    {
        $finalPrice = 150;
        $purchaseDto = new PurchaseDto(
            product: 1,
            taxNumber: 'DE123456789',
            couponCode: 'DISCOUNT10',
            paymentProcessor: PaymentMethod::PAYPAL->value
        );

        $priceCalculationDto = new PriceCalculationDto(
            product: 1,
            taxNumber: 'DE123456789',
            couponCode: 'DISCOUNT10'
        );

        $finalPriceRes = new PriceCalculationResultDto('success', $finalPrice);

        $this->priceCalculationService
            ->expects($this->once())
            ->method('calculatePrice')
            ->with($priceCalculationDto)
            ->willReturn($finalPriceRes);

        $paymentProcessorMock = $this->createMock(PaypalPaymentProcessorAdapter::class);
        $paymentProcessorMock
            ->expects($this->once())
            ->method('process')
            ->with($finalPrice)
            ->willReturn(true);

        $this->paymentProcessorFactory
            ->expects($this->once())
            ->method('getPaymentProcessor')
            ->with(PaymentMethod::PAYPAL->value)
            ->willReturn($paymentProcessorMock);


        $result = $this->purchaseService->handlePurchase($purchaseDto);

        $this->assertInstanceOf(PurchaseResultDto::class, $result);
        $this->assertEquals('success', $result->status);
        $this->assertEquals('uuid-format-like-string', $result->transactionUuid);
        $this->assertEquals(PaymentMethod::PAYPAL->value, $result->paymentProcessor);
        $this->assertEquals('Payment successfully made', $result->message);
    }

    public function testHandlePurchaseStripePaymentFailed(): void
    {
        $finalPrice = 80.5;
        $purchaseDto = new PurchaseDto(
            product: 1,
            taxNumber: 'DE123456789',
            couponCode: 'DISCOUNT10',
            paymentProcessor: PaymentMethod::STRIPE->value
        );

        $priceCalculationDto = new PriceCalculationDto(
            product: 1,
            taxNumber: 'DE123456789',
            couponCode: 'DISCOUNT10'
        );

        $finalPriceResDto = new PriceCalculationResultDto('success', $finalPrice);

        $this->priceCalculationService
            ->expects($this->once())
            ->method('calculatePrice')
            ->with($priceCalculationDto)
            ->willReturn($finalPriceResDto);

        // Mock the PaymentProcessor
        $paymentProcessorMock = $this->createMock(\App\Payment\StripePaymentProcessorAdapter::class);
        $paymentProcessorMock
            ->expects($this->once())
            ->method('process')
            ->with($finalPrice)
            ->willReturn(false);

        $this->paymentProcessorFactory
            ->expects($this->once())
            ->method('getPaymentProcessor')
            ->with(PaymentMethod::STRIPE->value)
            ->willReturn($paymentProcessorMock);


        $result = $this->purchaseService->handlePurchase($purchaseDto);

        $this->assertInstanceOf(PurchaseResultDto::class, $result);
        $this->assertEquals('error', $result->status);
        $this->assertEquals('uuid-format-like-string', $result->transactionUuid);
        $this->assertEquals(PaymentMethod::STRIPE->value, $result->paymentProcessor);
        $this->assertEquals('Payment process failed', $result->message);
    }


    public function testHandlePurchasePaypalPaymentFailed(): void
    {
        $finalPrice = 100000000;
        $purchaseDto = new PurchaseDto(
            product: 1,
            taxNumber: 'DE123456789',
            couponCode: 'DISCOUNT10',
            paymentProcessor: PaymentMethod::PAYPAL->value
        );

        $priceCalculationDto = new PriceCalculationDto(
            product: 1,
            taxNumber: 'DE123456789',
            couponCode: 'DISCOUNT10'
        );

        $finalPriceResDto = new PriceCalculationResultDto('success', $finalPrice);

        $this->priceCalculationService
            ->expects($this->once())
            ->method('calculatePrice')
            ->with($priceCalculationDto)
            ->willReturn($finalPriceResDto);

            
        $paymentProcessorMock = $this->createMock(PaypalPaymentProcessorAdapter::class);
        $paymentProcessorMock
            ->expects($this->once())
            ->method('process')
            ->with($finalPrice)
            ->willThrowException(new \Exception('Price issue'));

        $this->paymentProcessorFactory
            ->expects($this->once())
            ->method('getPaymentProcessor')
            ->with(PaymentMethod::PAYPAL->value)
            ->willReturn($paymentProcessorMock);


        $result = $this->purchaseService->handlePurchase($purchaseDto);

        $this->assertInstanceOf(PurchaseResultDto::class, $result);
        $this->assertEquals('error', $result->status);
        $this->assertEquals('uuid-format-like-string', $result->transactionUuid);
        $this->assertEquals(PaymentMethod::PAYPAL->value, $result->paymentProcessor);
        $this->assertEquals('Price issue', $result->message);
    }
}
