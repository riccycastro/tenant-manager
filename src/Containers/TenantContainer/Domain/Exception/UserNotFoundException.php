<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Exception;

use App\Containers\TenantContainer\Domain\ValueObject\UserId;

final class UserNotFoundException extends \RuntimeException
{
    public static function fromUserId(UserId $userId): self
    {
        return new self(
            sprintf('User with id %s not found', $userId->toString())
        );
    }
}
