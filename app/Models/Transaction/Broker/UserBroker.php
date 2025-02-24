<?php

class UserBroker extends BaseBroker
{
    public function __construct()
    {
        parent::__construct("users", User::class);
    }
}
