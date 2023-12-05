<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\PatchTenantInputDto;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;

/**
 * @implements ProcessorInterface<PatchTenantInputDto>
 */
final class UpdateTenantProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    // @phpstan-ignore-next-line
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TenantResource
    {
        assert($data instanceof PatchTenantInputDto);

        $command = new UpdateTenantCommand(
            code: TenantCode::fromString($uriVariables['code'] ?? null),
            status: $data->status ? TenantStatus::from($data->status) : null,
        );

        $tenant = $this->commandBus->dispatch($command);

        return TenantResource::fromModel($tenant);
    }
}
