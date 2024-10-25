<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\VirtualCard;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@shop.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpassword'));
        $manager->persist($admin);
        $this->addReference('admin-user', $admin);

        $this->createVirtualCard($manager, $admin);

        // Créer des utilisateurs normaux
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $this->addReference('user_'.$i, $user);

            $this->createVirtualCard($manager, $user);
        }

        $manager->flush();
    }

    private function createVirtualCard(ObjectManager $manager, User $user)
    {
        $faker = Factory::create('fr_FR');

        $virtualCard = new VirtualCard();
        $virtualCard->setUser($user);
        $virtualCard->setCardNumber($faker->creditCardNumber);
        $virtualCard->setCvv($faker->numerify('###'));
        $virtualCard->setExpirationDate($faker->creditCardExpirationDateString);
        $virtualCard->setBalance($faker->randomFloat(2, 100, 5000));
        $virtualCard->setActive(true);

        $manager->persist($virtualCard);
    }
}
