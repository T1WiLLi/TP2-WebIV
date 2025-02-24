<?php

class TransactionBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("transactions", Transaction::class);
    }

    public function findByUserId(int $userId): array
    {
        return $this->select("SELECT * FROM ? WHERE user_id = ?", [$this->getTableName(), $userId], function ($row) {
            return $this->mapToEntity($row);
        });
    }
}
