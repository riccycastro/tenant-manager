<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Containers\TenantContainer\Domain\Exception\TenantCodeAlreadyExistException;
use App\Containers\TenantContainer\Domain\Model\NewTenant;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;

final class CreateTenantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PersistsTenantInterface $persistTenant,
        private readonly FindsTenantInterface $findsTenant,
    ) {
    }

    public function __invoke(CreateTenantCommand $command): Tenant
    {
        if (null !== $this->findsTenant->withCode($command->code)->getResult()) {
            throw TenantCodeAlreadyExistException::fromCode($command->code);
        }

        $newTenant = new NewTenant(
            id: $command->id,
            name: $command->name,
            code: $command->code,
            domainEmail: $command->domainEmail,
            createdBy: $command->user,
        );

        return $this->persistTenant->saveAsNew($newTenant);
    }
}
