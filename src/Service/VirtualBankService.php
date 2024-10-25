<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\VirtualCard;
use App\Enums\TransactionStatusEnum;
use App\Repository\VirtualCardRepository;

readonly class VirtualBankService
{
    public function __construct(
        private VirtualCardRepository $virtualCardRepository,
        private TransactionManager    $transactionManager,
    ) {}

    public function getVirtualCardForUser(User $user): ?VirtualCard
    {
        return $this->virtualCardRepository->findOneBy(['user' => $user, 'active' => true]);
    }

    public function processTransaction(VirtualCard $card, float $amount, string $description): TransactionResult
    {
        if (!$card->isActive()) {
            return new TransactionResult(false, "La carte virtuelle n'est pas active.");
        }

        if ($card->getBalance() < $amount) {
            return new TransactionResult(false, "Solde insuffisant sur la carte virtuelle.");
        }

        $transaction = new Transaction();
        $transaction->setVirtualCard($card);
        $transaction->setAmount($amount);
        $transaction->setDescription($description);
        $transaction->setStatus(TransactionStatusEnum::COMPLETED->value);

        $card->setBalance($card->getBalance() - $amount);

        $this->transactionManager->create($transaction);

        return new TransactionResult(true, "Transaction réussie.", $transaction->getId());
    }
}
