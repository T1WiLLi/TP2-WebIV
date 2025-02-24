<?php

use Models\Core\Entity;

class User extends Entity implements JsonEntity
{
    public int $id;
    public string $username;
    public string $password;
    public string $firstname;
    public string $lastname;
    public string $email;
    public float $balance;
    public UserMember $type;

    public function toJson(bool $isDebugging = false): string
    {
        $dto = new stdClass();
        $dto->id = $this->id;
        $dto->username = $this->username;
        $dto->firstname = $this->firstname;
        $dto->lastname = $this->lastname;
        $dto->email = $this->email;
        $dto->balance = $this->balance;
        $dto->type = $this->type->value;

        if ($isDebugging) {
            $dto->password = $this->password;
        }

        return json_encode($dto, JSON_THROW_ON_ERROR);
    }
}
