<?php

namespace Models\Transaction\Entities;

use Models\Core\Entity;

class Transaction extends Entity
{
    public int $id;
    public int $user_id;
    public string $name;
    public float $price;
    public int $quantity;
    public float $total;
    public \DateTime $created_at;
}
