<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Exception;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InvalidTenantStatusWorkFlowException extends BadRequestHttpException
{
    /**
     * @param TenantStatus[] $possibleNextStatus
     */
    public static function fromTenantStatusWorkflow(TenantStatus $nextStatus, TenantStatus $currentStatus, array $possibleNextStatus): self
    {
        return new self(
            sprintf(
                '%s status is not a valid next status from %s, expected status are %s',
                $nextStatus->value,
                $currentStatus->value,
                implode(', ', array_reduce($possibleNextStatus, function (array $carry, TenantStatus $tenantStatus) {
                    $carry[] = $tenantStatus->value;

                    return $carry;
                }, []))
            )
        );
    }
}
