<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\Exception\TenantCodeAlreadyExistException;
use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Query\CheckTenantCodeAvailabilityQuery;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;

final class CreateTenantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PersistsTenantInterface $persistTenant,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(CreateTenantCommand $command): Tenant
    {
        if (!$this->queryBus->ask(new CheckTenantCodeAvailabilityQuery($command->code))) {
            throw TenantCodeAlreadyExistException::fromCode($command->code);
        }

        $tenant = new Tenant(
            id: $command->id,
            name: $command->name,
            code: $command->code,
            domainEmail: $command->domainEmail,
            createdBy: $command->user,
        );

        return $this->persistTenant->saveAsNew($tenant);
    }
}
