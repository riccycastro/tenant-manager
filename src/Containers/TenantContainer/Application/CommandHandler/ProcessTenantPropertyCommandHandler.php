<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\UpdatesTenantInterface;
use App\Containers\TenantContainer\Domain\Command\ProcessTenantPropertyCommand;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\TenantProperty;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyId;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;

final class ProcessTenantPropertyCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UpdatesTenantInterface $updatesTenant,
    ) {
    }

    public function __invoke(ProcessTenantPropertyCommand $command): Tenant
    {
        $tenant = $command->tenant;

        if ($tenant->hasProperty($command->name)) {
            $tenant = $tenant->updateProperty($command->name, $command->value);
        } else {
            $tenant = $tenant->addProperty(new TenantProperty(
                TenantPropertyId::create(),
                $command->name,
                $command->value,
                $command->user,
            ));
        }

        return $this->updatesTenant->save($tenant);
    }
}
