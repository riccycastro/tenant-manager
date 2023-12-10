<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\UpdatesTenantInterface;
use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;

final class UpdateTenantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly FindsTenantInterface $findsTenant,
        private readonly UpdatesTenantInterface $updatesTenant
    ) {
    }

    public function __invoke(UpdateTenantCommand $command): Tenant
    {
        $tenant = $this->findsTenant->withCode($command->code)->getResult();

        if (!$tenant instanceof Tenant) {
            throw TenantNotFoundException::fromTenantCode($command->code);
        }

        assert($tenant instanceof Tenant);

        $tenant = $tenant->setStatus(
            status: $command->status
        );

        return $this->updatesTenant->save($tenant);
    }
}
