<?php

class TokenBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("token", Token::class);
    }

    public function findByValue(string $value): ?Token
    {
        $row = $this->selectSingle("SELECT * FROM ? WHERE value = ?", [$this->getTableName(), $value]);
        return $row ? $this->mapToEntity($row) : null;
    }
}
