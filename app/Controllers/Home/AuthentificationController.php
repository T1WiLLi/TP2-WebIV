<?php

use Zephyrus\Application\Controller;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;

class AuthentificationController extends Controller
{

    #[Post("/login")]
    public function login(): Response
    {
        return $this->json("");
    }
}
