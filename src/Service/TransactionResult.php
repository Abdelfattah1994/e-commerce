<?php

namespace App\Service;

readonly class TransactionResult
{
    public function __construct(private bool $successful, private string $message, private ?string $transactionId = null)
    {
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
}
