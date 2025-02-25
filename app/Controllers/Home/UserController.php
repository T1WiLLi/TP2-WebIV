<?php

namespace Controllers\Home;

use Models\Exceptions\UserNotFound;
use Models\Transaction\Entities\User;
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

    /**
     * @var User | null
     */
    private ?User $currentUser = null;

    /**
     * @var string | null
     */
    private ?string $currentToken = null;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->transactionService = new TransactionService();
        $this->tokenService = new TokenService();
    }

    public function before(): ?Response
    {
        $token = $this->request->getArgument('token');
        if ($token !== null) {
            $this->currentToken = $token;
            if (!$this->tokenService->validateToken($token)) {
                return $this->abortForbidden("Le token est invalide.");
            }
            $this->currentUser = $this->retrieveUserBefore($token);
        }
        return null;
    }

    #[Get("/profile/{token}")]
    public function getProfile(string $token): Response
    {
        return $this->json([
            "token"     => $this->refreshToken(),
            "username"  => $this->currentUser->username,
            "firstname" => $this->currentUser->firstname,
            "lastname"  => $this->currentUser->lastname,
            "email"     => $this->currentUser->email,
            "balance"   => $this->currentUser->balance,
            "type"      => $this->currentUser->type,
        ], 200);
    }

    #[Put("/profile/{token}")]
    public function updateProfile(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();
        $this->userService->updateProfile(
            $this->currentUser->id,
            $data['firstname'] ?? $this->currentUser->firstname,
            $data['lastname'] ?? $this->currentUser->lastname,
            $data['email'] ?? $this->currentUser->email,
            $data['username'] ?? $this->currentUser->username
        );
        return $this->json([
            "token"   => $this->refreshToken(),
            "message" => "Le profil a été mis à jour avec succès."
        ], 200);
    }


    #[Put("/profile/{token}/password")]
    public function changePassword(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();
        try {
            $this->userService->changePassword(
                $this->currentUser->id,
                $data['old_password'] ?? '',
                $data['new_password'] ?? ''
            );
        } catch (\Exception $e) {
            return $this->abortBadRequest($e->getMessage());
        }
        return $this->json([
            "token"   => $this->refreshToken(),
            "message" => "Mot de passe modifié avec succès."
        ], 200);
    }

    #[Post("/profile/{token}/credits")]
    public function addCredits(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();
        try {
            $this->userService->addCredits($this->currentUser->id, (float)($data['credit'] ?? 0));
        } catch (\Exception $e) {
            return $this->abortBadRequest($e->getMessage());
        }
        return $this->json([
            "token"   => $this->refreshToken(),
            "message" => "Crédits ajoutés avec succès."
        ], 200);
    }

    #[Get("/profile/{token}/transactions")]
    public function getTransactionHistory(string $token): Response
    {
        $history = $this->transactionService->getTransactions($this->currentUser->id);
        return $this->json($history, 200);
    }

    #[Post("/profile/{token}/transactions")]
    public function performTransaction(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();
        try {
            $this->transactionService->createTransaction(
                $this->currentUser->id,
                $data['name'] ?? '',
                (float)($data['price'] ?? 0),
                (int)($data['quantity'] ?? 0)
            );
        } catch (\Exception $e) {
            return $this->abortBadRequest($e->getMessage());
        }
        return $this->json([
            "token"   => $this->refreshToken(),
            "message" => "Transaction créée avec succès."
        ], 201);
    }

    #[Post("/profile/{token}/elevate")]
    public function elevateAccount(string $token): Response
    {
        try {
            $this->userService->elevateAccount($this->currentUser->id);
        } catch (\Exception $e) {
            return $this->abortBadRequest($e->getMessage());
        }
        return $this->json([
            "token"   => $this->refreshToken(),
            "message" => "Le compte est maintenant PREMIUM."
        ], 200);
    }

    private function refreshToken(): string
    {
        $this->currentToken = $this->tokenService->refresh($this->currentToken, $this->currentUser->id)->value;
        return $this->currentToken;
    }

    private function retrieveUserBefore(string $token): User
    {
        $userID = $this->tokenService->getUserIDFromToken($token);
        $user = $this->userService->getProfile($userID);
        if (!$this->currentUser) {
            return $this->abortNotFound("L'utilisateur n'a pas été trouvé.");
        }
        return $user;
    }
}
