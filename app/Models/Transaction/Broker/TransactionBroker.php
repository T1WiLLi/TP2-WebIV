<?php

namespace Models\Transaction\Broker;

use Models\Transaction\Entities\Transaction;

class TransactionBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("transactions", Transaction::class);
    }

    public function findByUserId(int $userId): array
    {
        return $this->select("SELECT * FROM {$this->getTableName()} WHERE user_id = ?", [$userId], function ($row) {
            return $this->mapToEntity($row);
        });
    }
}
