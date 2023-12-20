<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\TenantOutputDto;

/**
 * @implements ProviderInterface<TenantOutputDto>
 */
final class TenantItemProvider implements ProviderInterface
{
    public function __construct(
        private readonly FindsTenantInterface $findsTenant
    ) {
    }

    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): TenantOutputDto {
        $tenantCode = TenantCode::fromString($uriVariables['code']);

        $tenant = $this->findsTenant->withCode($tenantCode)->getResult();

        if (null === $tenant) {
            throw TenantNotFoundException::fromTenantCode($tenantCode);
        }

        return TenantOutputDto::fromModel($tenant);
    }
}
