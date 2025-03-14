<?php

namespace Models\Transaction\Broker;

class TransactionBroker extends Broker
{
    public function __construct()
    {
        parent::__construct("transactions");
    }

    public function findByUserId(int $userId): array
    {
        return $this->select("SELECT * FROM {$this->table} WHERE user_id = ?", [$userId]);
    }
}
