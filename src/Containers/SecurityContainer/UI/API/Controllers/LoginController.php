<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\UI\API\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class LoginController
{
    #[Route(
        '/login',
        name: 'security_api_login',
        methods: ['GET']
    )]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello World!']);
    }
}
