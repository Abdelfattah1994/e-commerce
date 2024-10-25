<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case COMPLETED = 'completed';

    case PENDING = 'pending';
    case FAILED = 'failed';

    case CANCELLED = 'cancelled';

    case REFUNDED = 'refunded';

    public static function getValues(): array
    {
        return array_map(fn(TransactionStatusEnum $status) => $status->value, TransactionStatusEnum::cases());
    }
}
