<?php

namespace Models\Transaction\Entities;

use Models\Core\Entity;
use Models\Transaction\helper\JsonEntity;
use stdClass;

class Transaction extends Entity implements JsonEntity
{
    public int $id;
    public int $user_id;
    public string $name;
    public float $price;
    public int $quantity;
    public float $total;
    public \DateTime $created_at;

    public function toJson(bool $isDebugging = false): string
    {
        $dto = new stdClass();
        $dto->transaction_id = $this->id;
        $dto->user_id = $this->user_id;
        $dto->name = $this->name;
        $dto->price = $this->price;
        $dto->quantity = $this->quantity;
        $dto->total = $this->total;
        $dto->created_at = $this->created_at->format('Y-m-d H:i:s');

        return json_encode($dto, JSON_THROW_ON_ERROR);
    }
}
