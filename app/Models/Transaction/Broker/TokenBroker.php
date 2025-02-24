<?php

namespace Models\Transaction\Broker;

use Models\Transaction\Entities\Token;

class TokenBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("token", Token::class);
    }

    public function findByValue(string $value): ?Token
    {
        $row = $this->selectSingle("SELECT * FROM {$this->getTableName()} WHERE value = ?", [$value]);
        return $row ? $this->mapToEntity($row) : null;
    }
}
