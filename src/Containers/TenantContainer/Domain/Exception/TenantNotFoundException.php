<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Exception;

use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TenantNotFoundException extends NotFoundHttpException
{
    public static function fromFindTenantQuery(FindTenantQuery $query): self
    {
        if (null !== $query->code) {
            return new self(
                sprintf('Tenant with code %s not found', $query->code->toString())
            );
        }

        return new self();
    }

    public static function fromTenantCode(TenantCode $code): self
    {
        return new self(
            sprintf('Tenant with code %s not found', $code->toString())
        );
    }
}
