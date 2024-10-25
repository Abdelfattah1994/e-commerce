<?php

namespace App\DataFixtures;

use App\Entity\SupportTicket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SupportTicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $statuses = ['OPEN', 'IN_PROGRESS', 'CLOSED'];

        for ($i = 0; $i < 20; $i++) {
            $ticket = new SupportTicket();
            $ticket->setUser($this->getReference('user_'.rand(0, 9)));
            $ticket->setSubject($faker->sentence);
            $ticket->setMessage($faker->paragraph);
            $ticket->setStatus($faker->randomElement($statuses));

            $manager->persist($ticket);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
