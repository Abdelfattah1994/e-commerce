<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\VirtualCard;
use App\Enums\DatabaseTransaction;
use App\Repository\TransactionRepository;

readonly class TransactionManager
{

    public function __construct(private TransactionRepository $transactionRepository)
    {
    }

    public function createTransaction(VirtualCard $card, float $amount, string $description): Transaction
    {
        if ($amount < 0 && $card->getBalance() < abs($amount)) {
            throw new \Exception('Insufficient funds');
        }

        $transaction = new Transaction();
        $transaction->setVirtualCard($card);
        $transaction->setAmount($amount);
        $transaction->setDescription($description);

        $card->setBalance($card->getBalance() + $amount);

        $this->create($transaction);

        return $transaction;
    }
	
	public function create(Transaction $transaction)
	{
		$this->transactionRepository->save($transaction, DatabaseTransaction::COMMIT);
	}

    public function getTransactionsForCard(VirtualCard $card): array
    {
        return $this->transactionRepository->findBy(['virtualCard' => $card], ['createdAt' => 'DESC']);
    }
}
