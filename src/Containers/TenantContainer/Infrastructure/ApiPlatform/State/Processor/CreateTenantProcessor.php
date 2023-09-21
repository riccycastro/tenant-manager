<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Application\FindsLoggedUserInterface;

/**
 * @implements ProcessorInterface<TenantResource>
 */
final class CreateTenantProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly FindsLoggedUserInterface $findsLoggedUser,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TenantResource
    {
        assert($data instanceof TenantResource);

        $command = new CreateTenantCommand(
            TenantId::create(),
            TenantName::fromString($data->name),
            TenantCode::fromString($data->code),
            TenantDomainEmail::fromString($data->domainEmail),
            User::fromCoreUser($this->findsLoggedUser->getLoggedUser()),
        );

        $this->commandBus->dispatch($command);

        return $data;
    }
}
