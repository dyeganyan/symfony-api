<?php

namespace App\Tests\Service;

use App\Dto\PriceCalculationDto;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\DiscountType;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\PriceCalculationService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PriceCalculationServiceTest extends TestCase
{
    private ProductRepository $productRepository;
    private CouponRepository $couponRepository;
    private LoggerInterface $logger;
    private PriceCalculationService $priceCalculationService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->couponRepository = $this->createMock(CouponRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->priceCalculationService = new PriceCalculationService(
            productRepository: $this->productRepository,
            couponRepository: $this->couponRepository,
            logger: $this->logger
        );
    }

    /**
     * @dataProvider productAndCouponProvider
     */
    public function testCalculatePriceWithValidProductAndCoupon(Product $product, Coupon $coupon): void
    {

        $priceCalculationDto = new PriceCalculationDto(
            product: 1,
            taxNumber: 'DE123456',
            couponCode: $coupon->getCode()
        );

        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with($priceCalculationDto->product)
            ->willReturn($product);

        $this->couponRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $priceCalculationDto->couponCode])
            ->willReturn($coupon);

        $calculatedPrice = $this->priceCalculationService->calculatePrice($priceCalculationDto);

        $this->assertEquals(110, $calculatedPrice->finalPrice);
    }

    public function testCalculatePriceWithInvalidProduct(): void
    {
        $priceCalculationDto = new PriceCalculationDto(
            product: 999,
            taxNumber: 'DE123456',
            couponCode: 'DISCOUNT10'
        );

        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with($priceCalculationDto->product)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);        
        $this->priceCalculationService->calculatePrice($priceCalculationDto);
    }

    public static function productAndCouponProvider(): array
    {
        $product = new Product();
        $product->setName('TEST-PRODUCT');
        $product->setPrice(100);

        $coupon = new Coupon();
        $coupon->setCode('DISCOUNT10');
        $coupon->setDiscountType(DiscountType::PERCENTAGE);
        $coupon->setDiscountValue(10);

        return [
            'validProductAndCoupon' => [$product, $coupon],
        ];
    }
}
