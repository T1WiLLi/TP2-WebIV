<?php

namespace Models\Transaction\Entities;

use Models\Core\Entity;

class User extends Entity
{
    public int $id;
    public string $username;
    public string $password;
    public string $firstname;
    public string $lastname;
    public string $email;
    public float $balance;
    public string $type;
}
