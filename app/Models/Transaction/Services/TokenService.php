<?php

namespace Models\Transaction\Services;

use Models\Transaction\Broker\TokenBroker;
use Models\Transaction\Entities\Token;
use Zephyrus\Security\Cryptography;

class TokenService
{
    private TokenBroker $tokenBroker;

    public function __construct()
    {
        $this->tokenBroker = new TokenBroker();
    }

    public function createToken(int $userID): Token
    {
        $value = Cryptography::randomString(24);

        $token = new Token();
        $token->value = $value;
        $token->user_id = $userID;
        $token->created_at = (new \DateTime())->format("Y-m-d H:i:s");

        $id = $this->tokenBroker->insert($token);
        $token->id = $id;

        return $token;
    }

    public function validateToken(string $value): bool
    {
        $token = $this->tokenBroker->findByValue($value);

        if (null === $token) {
            return false;
        }

        $this->tokenBroker->delete($token);
    }

    public function getUserIDFromToken(string $value): int
    {
        $token = $this->tokenBroker->findByValue($value);
        return $token->user_id ?? '';
    }
}
