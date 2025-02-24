<?php


namespace Models\Transaction\Broker;

use Models\Transaction\Entities\User;

class UserBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("users", User::class);
    }

    public function findByUsername(string $username): ?User
    {
        $row = $this->selectSingle("SELECT * FROM {$this->getTableName()} WHERE username = ?", [$username]);
        return $row ? $this->mapToEntity($row) : null;
    }
}
