<?php

namespace Models\Transaction\Services;

use Models\Transaction\Broker\TransactionBroker;
use Models\Transaction\Broker\UserBroker;
use Models\Transaction\Entities\Transaction;
use Models\Transaction\Validators\TransactionValidator;
use RuntimeException;
use Zephyrus\Application\Form;

class TransactionService
{
    private TransactionBroker $transactionBroker;
    private UserBroker $userBroker;

    public function __construct()
    {
        $this->transactionBroker = new TransactionBroker();
        $this->userBroker = new UserBroker();
    }

    public function createTransaction(int $userId, string $name, float $price, int $quantity): void
    {
        $user = $this->userBroker->findById($userId);
        if (!$user) {
            throw new RuntimeException("Utilisateur introuvable.");
        }

        $form = new Form([
            "name"     => $name,
            "price"    => $price,
            "quantity" => $quantity
        ]);

        TransactionValidator::validateCreate($form, $user->type);

        $totalCost = $price * $quantity;
        if ($totalCost > $user->balance) {
            throw new RuntimeException("Fonds insuffisants pour cette transaction.");
        }

        $user->balance -= $totalCost;
        $this->userBroker->update($user);

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->name = $name;
        $transaction->price = $price;
        $transaction->quantity = $quantity;
        $transaction->total = $totalCost;
        $transaction->created_at = new \DateTime();

        $this->transactionBroker->insert($transaction);
    }

    public function getTransactions(int $userId): array
    {
        return $this->transactionBroker->findByUserId($userId);
    }
}
