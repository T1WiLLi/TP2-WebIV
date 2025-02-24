<?php

class UserBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("users", User::class);
    }

    public function findByUsername(string $username): ?User
    {
        $row = $this->selectSingle("SELECT * FROM ? WHERE username = ?", [$this->getTableName(), $username]);
        return $row ? $this->mapToEntity($row) : null;
    }
}
