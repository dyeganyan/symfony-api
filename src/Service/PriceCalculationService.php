<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Enum\DiscountType;
use Psr\Log\LoggerInterface;
use App\Dto\PriceCalculationDto;
use App\Enum\CountryCodeTaxMapping;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Dto\PriceCalculationResultDto;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PriceCalculationService
{
    private float $basePrice = 0;
    private float $taxRate = 0;
    private ?Coupon $coupon = null;

    public function __construct(
        private ProductRepository $productRepository,
        private CouponRepository $couponRepository,
        private LoggerInterface $logger
    ) {

    }

    /**
     * Calculate product price
     * @param \App\Dto\PriceCalculationDto $priceCalculationDto
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return PriceCalculationResultDto
     */
    function calculatePrice(PriceCalculationDto $priceCalculationDto): PriceCalculationResultDto
    {
        if (!$product = $this->productRepository->find($priceCalculationDto->product)) {
            throw new NotFoundHttpException('Product not found');
        }

        $calculationProcess = $this->setBasePrice($product->getPrice())
            ->addTax($priceCalculationDto->taxNumber);

        if ($priceCalculationDto->couponCode) {
            $coupon = $this->couponRepository->findOneBy(['code' => $priceCalculationDto->couponCode]);
            if (!$coupon) {
                $this->logger->error("PriceCalculationService@calculatePrice:coupon {$priceCalculationDto->couponCode} not found");
            }

            $calculationProcess->useCoupon($coupon);
        }

        return new PriceCalculationResultDto(status: 'success', finalPrice: $this->calculateFinalPrice());
    }

    /**
     * Set product base price
     * @param float $price
     * @return \App\Service\PriceCalculationService
     */
    private function setBasePrice(float $price): self
    {
        $this->basePrice = $price;
        return $this;
    }

    /**
     * Use discount
     * @param \App\Entity\Coupon $coupon
     * @return \App\Service\PriceCalculationService
     */
    private function useCoupon(?Coupon $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Add tax
     * @param string $taxNumber
     * @return \App\Service\PriceCalculationService
     */
    private function addTax(string $taxNumber): self
    {
        $this->taxRate = $this->getTaxRateByTaxNumber($taxNumber);
        return $this;
    }

    /**
     * Get tax rate by tax number
     * @param string $taxNumber
     * @return void
     */
    private function getTaxRateByTaxNumber(string $taxNumber): float
    {
        preg_match("/^[A-Z]{2}/", $taxNumber, $matches);
        $countryCode = strtoupper($matches[0]);

        return CountryCodeTaxMapping::{$countryCode}->value;
    }

    private function calculateFinalPrice(): float
    {
        $finalPrice = $this->basePrice + ($this->basePrice * $this->taxRate / 100);

        if ($this->coupon) {
            if ($this->coupon->getDiscountType() === DiscountType::FIXED_AMOUNT) {
                $finalPrice -= $this->coupon->getDiscountValue();
            }

            if ($this->coupon->getDiscountType() === DiscountType::PERCENTAGE) {
                $finalPrice = $finalPrice - $finalPrice % $this->coupon->getDiscountValue();
            }
        }

        return max(0, round($finalPrice, 2));
    }
}
