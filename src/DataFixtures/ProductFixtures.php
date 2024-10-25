<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $categories = $manager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->words(3, true));
            $product->setDescription($faker->paragraph());
            $product->setPrice($faker->randomFloat(2, 10, 1000));
            $product->setStock($faker->numberBetween(0, 100));
            $product->setFeatured($faker->boolean(20));
            
            // Sélectionner une catégorie aléatoire
            $randomCategory = $faker->randomElement($categories);
            $product->setCategory($this->getReference('category_' . $randomCategory->getId()));
            
            $product->setImage('images/default-product.png');
            $product->setActive(true);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
