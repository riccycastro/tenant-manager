<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\CheckTenantCodeAvailabilityCommand;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;

final class CheckTenantCodeAvailabilityCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly FindsTenantInterface $findsTenant
    ) {
    }

    public function __invoke(CheckTenantCodeAvailabilityCommand $command): bool
    {
        return null === $this->findsTenant->byCode($command->code);
    }
}
