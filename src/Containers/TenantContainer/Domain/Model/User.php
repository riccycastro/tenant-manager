<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Model;

use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Ship\Core\Domain\Model\LoggedUser;

final class User
{
    public function __construct(
        private UserId $id,
        private UserEmail $email,
    ) {
    }

    public static function fromCoreUser(LoggedUser $loggedUser): self
    {
        return new self(
            UserId::fromString($loggedUser->getId()),
            UserEmail::fromString($loggedUser->getEmail()),
        );
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }
}
