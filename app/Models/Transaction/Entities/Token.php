<?php

namespace Models\Transaction\Entities;

use Models\Core\Entity;
use Models\Transaction\helper\JsonEntity;
use stdClass;

class Token extends Entity implements JsonEntity
{
    public int $id;
    public string $value;
    public int $user_id;
    public string $created_at;

    public function toJson(bool $isDebugging = false): string
    {
        $dto = new stdClass();
        $dto->id = $this->id;
        $dto->user_id = $this->user_id;
        $dto->create_at = $this->created_at;

        if ($isDebugging) {
            $dto->value = $this->value;
        }

        return json_encode($dto, JSON_THROW_ON_ERROR);
    }
}
