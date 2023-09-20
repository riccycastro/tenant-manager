<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;

/**
 * @implements ProcessorInterface<TenantResource>
 */
final class CreateTenantProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        assert($data instanceof TenantResource);

        $command = new CreateTenantCommand(
            TenantName::fromString($data->name),
            TenantCode::fromString($data->code),
            TenantDomainEmail::fromString($data->domainEmail),
        );

        $this->commandBus->dispatch($command);

        return $data;
    }
}
