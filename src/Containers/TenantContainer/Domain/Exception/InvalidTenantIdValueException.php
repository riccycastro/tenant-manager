<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Exception;

final class InvalidTenantIdValueException extends \InvalidArgumentException
{
    public static function fromValue(string $value): self
    {
        return new self(
            sprintf('Value `%s` is not a valid tenant unique identifier', $value)
        );
    }
}
