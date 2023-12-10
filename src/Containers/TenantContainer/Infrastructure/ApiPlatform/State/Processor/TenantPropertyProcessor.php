<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\ProcessTenantPropertyCommand;
use App\Containers\TenantContainer\Domain\Enum\PropertyType;
use App\Containers\TenantContainer\Domain\Exception\InvalidPropertyTypeException;
use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyName;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyValue;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\TenantOutputDto;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Application\FindsLoggedUserInterface;

/**
 * @implements ProcessorInterface<TenantResource>
 */
final class TenantPropertyProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly FindsTenantInterface $findsTenant,
        private readonly FindsLoggedUserInterface $findsLoggedUser,
    ) {
    }

    /**
     * @throws InvalidPropertyTypeException
     */
    public function process(// @phpstan-ignore-line
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): TenantOutputDto {
        assert($data instanceof TenantResource);

        $tenant = $this->findsTenant->withCode(TenantCode::fromString($uriVariables['code']))->getResult();

        if (null === $tenant) {
            throw TenantNotFoundException::fromTenantCode(TenantCode::fromString($uriVariables['code']));
        }

        foreach ($data->properties as $property) {
            $command = new ProcessTenantPropertyCommand(
                $tenant,
                TenantPropertyName::fromString($property->name),
                TenantPropertyValue::fromValueType(
                    PropertyType::from($property->type),
                    $property->value,
                ),
                User::fromCoreUser($this->findsLoggedUser->getLoggedUser()),
            );

            $tenant = $this->commandBus->dispatch($command);
        }

        return TenantOutputDto::fromModel($tenant);
    }
}
