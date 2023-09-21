<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\Exception;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

final class TenantCodeAlreadyExistException extends ConflictHttpException
{
    public static function fromCode(TenantCode $code): self
    {
        return new self(
            sprintf('Tenant with code %s already exists', $code->code)
        );
    }
}
