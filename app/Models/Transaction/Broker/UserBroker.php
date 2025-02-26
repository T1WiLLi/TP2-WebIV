<?php


namespace Models\Transaction\Broker;

use Models\Transaction\Entities\User;

class UserBroker extends Broker
{
    public function __construct()
    {
        parent::__construct("users", User::class);
    }

    public function findByUsername(string $username): ?User
    {
        $row = $this->selectSingle("SELECT * FROM {$this->table} WHERE username = ?", [$username]);
        return $row ? $row : null;
    }

    public function findByEmail(string $email): ?User
    {
        $row = $this->selectSingle("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
        return $row ? $row : null;
    }
}
