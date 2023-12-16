<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Exception;

use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyName;

final class TenantPropertyNotFound extends \Exception
{
    public static function fromTenantPropertyName(TenantPropertyName $name): self
    {
        return new self(
            sprintf('Tenant Property with name %s not found', $name->toString())
        );
    }
}
