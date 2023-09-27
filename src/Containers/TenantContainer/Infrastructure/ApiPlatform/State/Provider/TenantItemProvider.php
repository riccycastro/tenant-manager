<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;

/**
 * @implements ProviderInterface<TenantResource>
 */
final class TenantItemProvider implements ProviderInterface
{
    public function __construct(
        private readonly FindsTenantInterface $findsTenant
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $tenantCode = TenantCode::fromString($uriVariables['code']);

        $tenant = $this->findsTenant->withCode($tenantCode)->getResult();

        return TenantResource::fromModel($tenant);
    }
}
