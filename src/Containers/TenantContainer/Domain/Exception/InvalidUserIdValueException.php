<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Exception;

final class InvalidUserIdValueException extends \InvalidArgumentException
{
    public static function fromValue(string $value): self
    {
        return new self(
            sprintf('Value `%s` is not a valid user unique identifier', $value)
        );
    }
}
