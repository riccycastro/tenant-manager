<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;

final class CreateTenantCommandHandler implements CommandHandlerInterface
{
    public function __invoke(CreateTenantCommand $command): void
    {
        dd($command);
    }
}
