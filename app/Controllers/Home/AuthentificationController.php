<?php

namespace Controllers\Home;

use Models\Exceptions\FormException;
use Models\Transaction\Services\TokenService;
use Models\Transaction\Services\UserService;
use RuntimeException;
use Zephyrus\Application\Controller;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;

class AuthentificationController extends Controller
{

    #[Post("/login")]
    public function login(): Response
    {
        $data = $this->request->getBody()->getParameters();
        $username = $data["username"] ?? null;
        $password = $data["password"] ?? null;

        try {
            $userService = new UserService();
            $isAuthenticated = $userService->login($username, $password);

            if (!$isAuthenticated) {
                return $this->abortNotFound("Identifiants d'authentification invalides.");
            }

            $id = $userService->getIdByUsername($username);
            $tokenService = new TokenService();
            $token = $tokenService->refresh("", $id);

            return $this->json([
                "token" => $token->value,
                "message" => "Authentification réussie."
            ], 200);
        } catch (FormException $e) {
            return $this->json([
                "message" => "Erreur de validation.",
                "errors" => $e->getForm()
            ], 400);
        } catch (RuntimeException $e) {
            return $this->json([
                "message" => $e->getMessage()
            ], 400);
        }
    }

    #[Post("/register")]
    public function register(): Response
    {
        $formData = $this->request->getBody()->getParameters();

        try {
            $userService = new UserService();
            $userService->register($formData);

            return $this->json([
                'message' => "User registered successfully."
            ], 201);
        } catch (RuntimeException $e) {
            return $this->json([
                "message" => $e->getMessage()
            ], 400);
        } catch (FormException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
