<?php

namespace Models\Transaction\Services;

use Models\Exceptions\UserNotFound;
use Models\Transaction\Broker\UserBroker;
use Models\Transaction\Entities\User;
use Models\Transaction\helper\UserMember;
use Models\Transaction\Validators\UserValidator;
use RuntimeException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class UserService
{
    private UserBroker $userBroker;

    public function __construct()
    {
        $this->userBroker = new UserBroker();
    }

    public function login(string $username, string $password): bool
    {
        $form = new Form([
            "username" => $username,
            "password" => $password
        ]);

        UserValidator::validateLogin($form);

        $user = $this->userBroker->findByUsername($username);
        if (!$user) {
            return false;
        }

        if (!Cryptography::verifyHashedPassword($password, $user->password)) {
            return false;
        }

        return true;
    }

    public function register(array $formData)
    {
        // UserValidator::validateRegistration($formData);

        if (
            $this->userBroker->findByUsername($formData['username']) ||
            $this->userBroker->findByEmail($formData['email'])
        ) {
            throw new RuntimeException("Username or Email already taken.");
            return;
        }

        $hashedPassword = Cryptography::hashPassword($formData['password']);

        $user = new User();
        $user->username = $formData['username'];
        $user->password = $hashedPassword;
        $user->firstname = $formData['firstname'];
        $user->lastname = $formData['lastname'];
        $user->email = $formData['email'];
        $user->balance = 0.0;
        $user->type = 'NORMAL';

        $this->userBroker->insert($user);
    }


    public function getProfile(int $userId): ?User
    {
        return $this->userBroker->findById($userId);
    }

    public function updateProfile(
        int $userId,
        string $firstname,
        string $lastname,
        string $email,
        string $username
    ): void {
        $form = new Form([
            "firstname" => $firstname,
            "lastname"  => $lastname,
            "email"     => $email,
            "username"  => $username
        ]);

        UserValidator::validateProfile($form);

        $user = $this->userBroker->findById($userId);
        if (!$user) {
            throw new RuntimeException("Utilisateur introuvable.");
        }

        $existing = $this->userBroker->findByUsername($username);
        if ($existing && $existing->id !== $user->id) {
            throw new RuntimeException("Le nom d’utilisateur est déjà pris.");
        }

        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->email = $email;
        $user->username = $username;

        $this->userBroker->update($user);
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword): void
    {
        $form = new Form([
            "oldPassword" => $oldPassword,
            "newPassword" => $newPassword
        ]);

        UserValidator::validatePasswordChange($form);

        $user = $this->userBroker->findById($userId);
        if (!$user) {
            throw new UserNotFound("Utilisateur introuvable.");
        }

        if ($user->password !== $oldPassword) {
            throw new RuntimeException("L'ancien mot de passe est invalide.");
        }

        $user->password = $newPassword;
        $this->userBroker->update($user);
    }

    public function addCredits(int $userId, float $amount): void
    {
        $form = new Form([
            "credit" => $amount
        ]);

        UserValidator::validateCredit($form);

        $user = $this->userBroker->findById($userId);
        if (!$user) {
            throw new UserNotFound("Utilisateur introuvable.");
        }

        if ($user->type === "NORMAL" && $amount > 500) {
            throw new RuntimeException("Montant trop élevé (max 500$ pour un compte NORMAL).");
        }

        if ($user->type === "PREMIUM" && $amount > 2000) {
            throw new RuntimeException("Montant trop élevé (max 2000$ pour un compte PREMIUM).");
        }

        $user->balance += $amount;
        $this->userBroker->update($user);
    }

    public function getIdByUsername(string $username): int
    {
        return $this->userBroker->findByUsername($username)->id;
    }
}
