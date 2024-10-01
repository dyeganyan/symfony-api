<?php

namespace App\DataFixtures;

use App\Entity\Coupon;
use App\Enum\DiscountType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $insertData = [
            [
                'code' => 'P10',
                'discountType' => DiscountType::PERCENTAGE,
                'discountValue' => '100'
            ],
            [
                'code' => 'P80',
                'discountType' => DiscountType::PERCENTAGE,
                'discountValue' => '80'
            ],
            [
                'code' => 'P100',
                'discountType' => DiscountType::PERCENTAGE,
                'discountValue' => '100'
            ],
            [
                'code' => 'FA5',
                'discountType' => DiscountType::FIXED_AMOUNT,
                'discountValue' => '5'
            ]
        ];

        foreach ($insertData as $data) {

            $coupon = new Coupon();
            $coupon->setCode($data['code']);
            $coupon->setDiscountType($data['discountType']);
            $coupon->setDiscountValue($data['discountValue']);

            $manager->persist($coupon);
        }

        $manager->flush();
    }
}
