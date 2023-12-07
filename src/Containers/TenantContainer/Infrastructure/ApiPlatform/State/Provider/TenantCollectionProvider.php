<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Query\FindTenantsListQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\TenantOutputDto;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use App\Ship\Core\Domain\Repository\Dto\ModelList;
use App\Ship\Core\Infrastructure\ApiPlatform\State\Paginator;

/**
 * @implements ProviderInterface<TenantOutputDto>
 */
final class TenantCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private Pagination $pagination,
    ) {
    }

    /**
     * @return Paginator<TenantOutputDto>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator
    {
        if (!$operation instanceof CollectionOperationInterface) {
            throw new \RuntimeException('This provider should be use with GetCollection operation');
        }

        $codeFilter = $context['filters']['code'] ?? null;
        $offset = $limit = null;

        if ($this->pagination->isEnabled($operation, $context)) {
            $offset = $this->pagination->getPage($context);
            $limit = $this->pagination->getLimit($operation, $context);
        }

        $query = new FindTenantsListQuery(
            $codeFilter ? TenantCode::fromString($codeFilter) : null,
            $offset,
            $limit,
        );

        /** @var ModelList<Tenant> $tenantModelList */
        $tenantModelList = $this->queryBus->ask($query);

        $resources = [];

        foreach ($tenantModelList->items as $tenant) {
            $resources[] = TenantOutputDto::fromModel($tenant);
        }

        return new Paginator(
            new \ArrayIterator($resources),
            $offset,
            $limit,
            (int) max(1, ceil($tenantModelList->count / $limit)),
            $tenantModelList->count,
        );
    }
}
