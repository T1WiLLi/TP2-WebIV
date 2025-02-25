<?php

namespace Models\Transaction\Entities;

use Models\Core\Entity;

class Token extends Entity
{
    public int $id;
    public string $value;
    public int $user_id;
    public string $created_at;
}
