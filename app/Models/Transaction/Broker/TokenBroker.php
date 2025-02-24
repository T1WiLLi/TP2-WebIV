<?php

class TokenBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("token", Token::class);
    }
}
