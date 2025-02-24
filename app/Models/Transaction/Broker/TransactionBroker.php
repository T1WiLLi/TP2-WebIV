<?php

class TransactionBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("transactions", Transaction::class);
    }
}
