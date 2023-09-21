<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;

final class CreateTenantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PersistsTenantInterface $persistTenant
    ) {
    }

    public function __invoke(CreateTenantCommand $command): Tenant
    {
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
