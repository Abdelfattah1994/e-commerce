<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $products = $manager->getRepository(Product::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $order = new Order();
            $order->setUser($this->getReference('user_'.rand(0, 9)));
            $order->setStatus($faker->randomElement(['PENDING', 'VALIDATED', 'REFUSED', 'COMPLETED']));
            $order->setTotalAmount(0);

            $numberOfItems = $faker->numberBetween(1, 5);
            $totalAmount = 0;

            for ($j = 0; $j < $numberOfItems; $j++) {
                $orderItem = new OrderItem();
                $product = $faker->randomElement($products);
                $quantity = $faker->numberBetween(1, 3);

                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice($product->getPrice());

                $order->addItem($orderItem);
                $totalAmount += $product->getPrice() * $quantity;
            }

            $order->setTotalAmount($totalAmount);

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ProductFixtures::class,
        ];
    }
}
