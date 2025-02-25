<?php

namespace Controllers\Home;

use Models\Exceptions\UserNotFound;
use Models\Transaction\Services\TokenService;
use Models\Transaction\Services\TransactionService;
use Models\Transaction\Services\UserService;
use RuntimeException;
use Zephyrus\Application\Controller;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Put;

class UserController extends Controller
{
    private UserService $userService;
    private TransactionService $transactionService;
    private TokenService $tokenService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->transactionService = new TransactionService();
        $this->tokenService = new TokenService();
    }

    #[Get("/profile/{token}")]
    public function getProfile(string $token): Response
    {
        if (!$this->tokenService->validateToken($token)) {
            return $this->abortForbidden("Le token est invalide");
        }

        $profile = $this->userService->getProfile($this->tokenService->getUserIDFromToken($token));
        $token = $this->tokenService->refresh($token, $profile->id);
        return $this->json([
            "token" => $token->value,
            "username" => $profile->username,
            "firstname" => $profile->firstname,
            "lastname" => $profile->lastname,
            "email" => $profile->email,
            "balance" => $profile->balance,
            "type" => $profile->type,
        ], 200);
    }

    #[Put("/profile/{token}")]
    public function updateProfile(string $token): Response
    {
        if (!$this->tokenService->validateToken($token)) {
            return $this->abortForbidden("Le token est invalide");
        }

        $user = $this->userService->getProfile($this->tokenService->getUserIDFromToken($token));
        $data = $this->request->getBody()->getParameters();

        $this->userService->updateProfile(
            $user->id,
            $data['firstname'] ?? $user->firstname,
            $data['lastname'] ?? $user->lastname,
            $data['email'] ?? $user->email,
            $data['username'] ?? $user->username
        );

        return $this->json([
            "token" => $this->tokenService->refresh($token, $user->id)->value,
            "message" => "Le profil a été mis à jour avec succès"
        ], 200);
    }

    #[Put("/profile/{token}/password")]
    public function changePassword(string $token): Response
    {
        if (!$this->tokenService->validateToken($token)) {
            return $this->abortForbidden("Le token est invalide");
        }

        $data = $this->request->getBody()->getParameters();
        $user = $this->userService->getProfile($this->tokenService->getUserIDFromToken($token));

        try {
            $this->userService->changePassword(
                $user->id,
                $data['old_password'] ?? '',
                $data['new_password'] ?? ''
            );
        } catch (UserNotFound $e) {
            return $this->abortNotFound("L'utilisateur n'a pas été trouvé");
        } catch (RuntimeException $e) {
            return $this->abortBadRequest($e->getMessage());
        }

        return $this->json([
            "token" => $this->tokenService->refresh($token, $user->id),
            "message" => "Mot de passe modifié avec succès."
        ]);
    }

    #[Post("/profile/{token}/credits")]
    public function addCredits(string $token): Response
    {
        if (!$this->tokenService->validateToken($token)) { // Add as lifecycle for everywhere request
            return $this->abortForbidden("Le token est invalide");
        }

        $data = $this->request->getBody()->getParameters();
        $user = $this->userService->getProfile($this->tokenService->getUserIDFromToken($token));

        try {
            $this->userService->addCredits(
                $user->id,
                (float)($data['credit'] ?? 0)
            );
        } catch (UserNotFound $e) {
            return $this->abortNotFound("L'utilisateur n'a pas été trouvé");
        } catch (RuntimeException $e) {
            return $this->abortBadRequest($e->getMessage());
        }
    }

    #[Get("/profile/{token}/transactions")]
    public function getTransactionHistory(string $token): Response
    {
        if (!$this->tokenService->validateToken($token)) { // Add as lifecycle for everywher
            return $this->abortForbidden("Le token est invalide");
        }

        $history = $this->transactionService->getTransactions($this->tokenService->getUserIDFromToken($token));
        return $this->json($history, 200);
    }

    #[Post("/profile/{token}/transactions")]
    public function performTransaction(string $token): Response
    {
        if (!$this->tokenService->validateToken($token)) {
            return $this->abortForbidden("Le token est invalide");
        }

        $data = $this->request->getBody()->getParameters();
        $user = $this->userService->getProfile($this->tokenService->getUserIDFromToken($token));

        try {
            $this->transactionService->createTransaction(
                $user->id,
                $data['name'] ?? '',
                (float)($data['price'] ?? 0),
                (int)($data['quantity'] ?? 0)
            );
        } catch (RuntimeException $e) {
            return $this->abortBadRequest($e->getMessage());
        }

        return $this->json([
            "token" => $this->tokenService->refresh($token, $user->id),
            "message" => "Transaction crée avec succès",
        ], 201);
    }

    #[Post("/profile/{token}/elevate")]
    public function elevateAccount(string $token): Response
    {
        if (!$this->tokenService->validateToken($token)) {
            return $this->abortForbidden("Le token est invalide");
        }

        try {
            $this->userService->elevateAccount($this->tokenService->getUserIDFromToken($token));
        } catch (UserNotFound $e) {
            return $this->abortNotFound("L'utilisateur n'a pas été trouvé");
        }

        return $this->json([
            "token" => $this->tokenService->refresh($token, $this->tokenService->getUserIDFromToken($token)),
            "message" => "Le compte est maintenant PREMIUM."
        ]);
    }
}
