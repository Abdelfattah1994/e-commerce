<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            'Électronique',
            'Vêtements',
            'Livres',
            'Maison et Jardin',
            'Sports et Loisirs',
        ];

        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setDescription("Description de la catégorie $categoryName");
            $category->setActive(true);
            $category->setImage('images/default-category.png');
            $manager->persist($category);
            
            // Flush après chaque catégorie pour obtenir l'ID
            $manager->flush();
            
            // Utiliser l'ID de la catégorie pour la référence
            $this->addReference('category_' . $category->getId(), $category);
        }
    }
}
