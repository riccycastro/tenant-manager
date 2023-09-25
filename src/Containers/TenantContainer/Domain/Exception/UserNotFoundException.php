<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Exception;

use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UserNotFoundException extends NotFoundHttpException
{
    public static function fromUserId(UserId $userId): self
    {
        return new self(
            sprintf('User with id %s not found', $userId->toString())
        );
    }
}
