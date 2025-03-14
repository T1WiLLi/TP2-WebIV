<?php


namespace Models\Transaction\Broker;

use Models\Transaction\Entities\User;
use stdClass;

class UserBroker extends Broker
{
    public function __construct()
    {
        parent::__construct("users");
    }

    public function findByUsername(string $username): ?stdClass
    {
        $row = $this->selectSingle("SELECT * FROM {$this->table} WHERE username = ?", [$username]);
        return $row ? $row : null;
    }

    public function findByEmail(string $email): ?stdClass
    {
        $row = $this->selectSingle("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
        return $row ? $row : null;
    }
}
