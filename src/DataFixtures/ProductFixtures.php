<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $insertData = [
            [
                'name' => 'IPhone 16',
                'description' => 'Стильный и мощный смартфон с отличной камерой и высокопроизводительным процессором.',
                'price' => '100',
                'quantity' => 200
            ],
            [
                'name' => 'Наушники',
                'description' => 'Удобные наушники с чистым звуком и глубокими басами.',
                'price' => '20',
                'quantity' => 500
            ],
            [
                'name' => 'Чехол',
                'description' => 'Прочный чехол, обеспечивающий защиту вашего смартфона от ударов и царапин.',
                'price' => '10',
                'quantity' => 1000
            ]
        ];

        foreach ($insertData as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setQuantity($data['quantity']);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
