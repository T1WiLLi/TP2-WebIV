<?php

namespace Models\Transaction\Broker;

use Models\Transaction\Entities\Transaction;

class TransactionBroker extends Broker
{
    public function __construct()
    {
        parent::__construct("transactions", Transaction::class);
    }

    public function findByUserId(int $userId): array
    {
        return $this->select("SELECT * FROM {$this->table} WHERE user_id = ?", [$userId]);
    }
}
