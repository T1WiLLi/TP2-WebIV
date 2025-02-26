<?php

namespace Models\Transaction\Broker;

use Models\Transaction\Entities\Token;
use stdClass;

class TokenBroker extends Broker
{
    public function __construct()
    {
        parent::__construct("token");
    }

    public function findByValue(string $value): ?stdClass
    {
        $row = $this->selectSingle("SELECT * FROM {$this->table} WHERE value = ?", [$value]);
        return $row ? $row : null;
    }
}
