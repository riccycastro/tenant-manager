<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Test\Acceptance\Component;

use App\Containers\SecurityContainer\Domain\Model\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class TokenFactory
{
    public function __construct(
        private readonly JWTTokenManagerInterface $JWTTokenManager,
    ) {
    }

    public function create(User $user): string
    {
        return $this->JWTTokenManager->create($user);
    }
}
