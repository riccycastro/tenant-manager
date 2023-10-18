<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\UpdateTenantProcessor;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\UpdateTenantProcessor
 *
 * @uses   \App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand
 * @uses   \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses   \App\Containers\TenantContainer\Domain\Model\User
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserId
 * @uses   \App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource
 */
final class UpdateTenantProcessorTest extends TestCase
{
    use ProphecyTrait;

    private UpdateTenantProcessor $sut;
    private ObjectProphecy|CommandBusInterface $commandBus;

    public function testItIsProcessorInterface(): void
    {
        self::assertInstanceOf(ProcessorInterface::class, $this->sut);
    }

    public function testProcessDispatchCreateTenantCommand(): void
    {
        $tenant = new Tenant(
            TenantId::create(),
            TenantName::fromString('nameless'),
            TenantCode::fromString('a_kode'),
            TenantDomainEmail::fromString('@site.com'),
            new User(
                UserId::create(),
                UserEmail::fromString('user@site.com')
            ),
            TenantStatus::READY,
            true,
        );

        $this->commandBus
            ->dispatch(Argument::type(UpdateTenantCommand::class))
            ->shouldBeCalledOnce()
            ->willReturn($tenant)
        ;

        $tenantResource = new TenantResource(
            name: 'tenant_name',
            code: 'a_code',
            domainEmail: '@tenant.com',
            status: TenantStatus::READY->value,
        );

        $operation = $this->prophesize(Operation::class)->reveal();

        $result = $this->sut->process($tenantResource, $operation);

        self::assertInstanceOf(TenantResource::class, $result);
    }

    protected function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBusInterface::class);

        $this->sut = new UpdateTenantProcessor(
            $this->commandBus->reveal(),
        );
    }
}
