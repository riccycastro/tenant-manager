<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\UpdatesTenantInterface;
use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;

final class UpdateTenantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly UpdatesTenantInterface $updatesTenant
    ) {
    }

    public function __invoke(UpdateTenantCommand $command): Tenant
    {
        $tenant = $this->queryBus->ask(new FindTenantQuery($command->code));

        assert($tenant instanceof Tenant);

        $tenant = $tenant->update(
            status: $command->status
        );

        return $this->updatesTenant->save($tenant);
    }
}
