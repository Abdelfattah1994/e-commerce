<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\VirtualCard;
use App\Enums\DatabaseTransaction;
use App\Repository\VirtualCardRepository;
use Faker\Factory;
use Symfony\Component\String\ByteString;

readonly class CardManager
{
    public function __construct(
        private VirtualCardRepository  $virtualCardRepository
    ) {
    }

    public function createCardForUser(User $user, float $initialBalance = 5000.0): VirtualCard
    {
        $card = new VirtualCard();
        $card->setUser($user);
        $card->setCardNumber($this->generateUniqueCardNumber());
        $card->setExpirationDate($this->generateExpirationDate());
        $card->setCvv($this->generateCvv());
        $card->setBalance($initialBalance);
        $card->setActive(true);

        $this->virtualCardRepository->save($card, DatabaseTransaction::COMMIT);

        return $card;
    }

    protected function generateCvv(): string
    {
        return ByteString::fromRandom(3)->toString();
    }

    protected function generateExpirationDate(): string
    {
        return (new \DateTime('+1 year'))->format('m/y');
    }

    public function deactivateCard(VirtualCard $card): void
    {
        $card->setActive(false);
        $this->virtualCardRepository->save($card, DatabaseTransaction::COMMIT);
    }

    public function reactivateCard(VirtualCard $card): void
    {
        $card->setActive(true);
	    $this->virtualCardRepository->save($card, DatabaseTransaction::COMMIT);
    }

    private function generateUniqueCardNumber(): string
    {
        $faker = Factory::create('fr_FR');
        do {
            $cardNumber = $faker->creditCardNumber;
        } while ($this->virtualCardRepository->findOneBy(['cardNumber' => $cardNumber]));

        return $cardNumber;
    }
}
