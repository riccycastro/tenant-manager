<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\MessageHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\MessageHandler\CreateTenantDatabaseMessageHandler;
use App\Containers\TenantContainer\Infrastructure\Service\DatabaseServiceInterface;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Application\Context;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use App\Ship\Core\Domain\Model\LoggedUser;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\MessageHandler\CreateTenantDatabaseMessageHandler
 *
 * @uses   \App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand
 * @uses   \App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage
 * @uses   \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses   \App\Containers\TenantContainer\Domain\Model\User
 * @uses   \App\Containers\TenantContainer\Domain\Query\FindTenantQuery
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class CreateTenantDatabaseMessageHandlerTest extends TestCase
{
    use ProphecyTrait;

    private readonly CreateTenantDatabaseMessageHandler $sut;
    private readonly ObjectProphecy|QueryBusInterface $findsTenant;
    private readonly ObjectProphecy|CommandBusInterface $commandBus;
    private readonly ObjectProphecy|DatabaseServiceInterface $tenantDatabaseService;

    public function testExecutionShouldStopIfStatusIsNotWaitingProvisioning(): void
    {
        $tenant = $this->generateTenant();
        $message = new CreateTenantDatabaseMessage($tenant->getCode());

        $this->findsTenant
            ->withCode($tenant->getCode())
            ->shouldBeCalled()
            ->willReturn($this->findsTenant)
        ;

        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn($tenant)
        ;

        $this->tenantDatabaseService
            ->hasDatabase(Argument::any())
            ->shouldNotBeCalled()
        ;

        ($this->sut)($message);
    }

    private function generateTenant(TenantStatus $tenantStatus = TenantStatus::READY): Tenant
    {
        return new Tenant(
            TenantId::create(),
            TenantName::fromString('nameless'),
            TenantCode::fromString('a_kode'),
            TenantDomainEmail::fromString('@site.com'),
            new User(
                UserId::create(),
                UserEmail::fromString('user@site.com')
            ),
            $tenantStatus,
            true,
            [],
        );
    }

    public function testIfDatabaseExistItChangeTenantStatusToReadyForMigration(): void
    {
        $tenant = $this->generateTenant(TenantStatus::WAITING_PROVISIONING);
        $message = new CreateTenantDatabaseMessage($tenant->getCode());

        $this->findsTenant
            ->withCode($tenant->getCode())
            ->shouldBeCalled()
            ->willReturn($this->findsTenant)
        ;

        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn($tenant)
        ;

        $this->tenantDatabaseService
            ->hasDatabase($tenant->getCode())
            ->shouldBeCalledOnce()
            ->willReturn(true)
        ;

        $this->tenantDatabaseService
            ->hasUser($tenant->getCode())
            ->shouldBeCalledOnce()
            ->willReturn(true)
        ;

        $self = $this;
        $tenantCode = $tenant->getCode();

        $this->commandBus
            ->dispatch(Argument::type(UpdateTenantCommand::class))
            ->should(function (array $data) use ($self, $tenantCode) {
                /** @var Call $call */
                $call = $data[0];

                /** @var UpdateTenantCommand $updateTenantCommand */
                $updateTenantCommand = $call->getArguments()[0];

                $self->assertEquals($tenantCode, $updateTenantCommand->code);
                $self->assertEquals(TenantStatus::READY_FOR_MIGRATION, $updateTenantCommand->status);
            })
            ->willReturn($tenant)
        ;

        ($this->sut)($message);
    }

    public function testWaitingProvisioningStatusIsSetOnException(): void
    {
        $tenant = $this->generateTenant(TenantStatus::WAITING_PROVISIONING);
        $message = new CreateTenantDatabaseMessage($tenant->getCode());

        $this->findsTenant
            ->withCode($tenant->getCode())
            ->shouldBeCalled()
            ->willReturn($this->findsTenant)
        ;

        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn($tenant)
        ;

        $this->tenantDatabaseService
            ->hasDatabase($tenant->getCode())
            ->shouldBeCalledOnce()
            ->willReturn(false)
        ;

        $this->tenantDatabaseService
            ->createDatabase($tenant->getCode())
            ->willThrow(new \Exception())
        ;

        $this->commandBus
            ->dispatch(new UpdateTenantCommand($tenant->getCode(), TenantStatus::PROVISIONING))
            ->shouldBeCalled()
            ->willReturn($tenant)
        ;

        $this->commandBus
            ->dispatch(new UpdateTenantCommand($tenant->getCode(), TenantStatus::WAITING_PROVISIONING))
            ->shouldBeCalled()
        ;

        ($this->sut)($message);
    }

    public function testItReproducesTheExpectedResult(): void
    {
        $message = new CreateTenantDatabaseMessage(TenantCode::fromString('any'));
        $tenant = $this->generateTenant(TenantStatus::WAITING_PROVISIONING);

        $this->findsTenant
            ->ask(Argument::type(FindTenantQuery::class))
            ->shouldBeCalled()
            ->willReturn($tenant)
        ;

        $this->tenantDatabaseService
            ->hasDatabase($tenant->getCode())
            ->shouldBeCalledOnce()
            ->willReturn(false)
        ;

        $this->tenantDatabaseService
            ->createDatabase($tenant->getCode())
            ->shouldBeCalledOnce()
        ;

        $this->tenantDatabaseService
            ->createTenantDatabaseUser($tenant->getCode(), Argument::any())
        ;

        $this->commandBus
            ->dispatch(new UpdateTenantCommand($tenant->getCode(), TenantStatus::PROVISIONING))
            ->shouldBeCalled()
        ;

        $this->commandBus
            ->dispatch(new UpdateTenantCommand($tenant->getCode(), TenantStatus::READY_FOR_MIGRATION))
            ->shouldBeCalled()
        ;

        ($this->sut)($message);
    }

    protected function setUp(): void
    {
        $this->findsTenant = $this->prophesize(FindsTenantInterface::class);
        $this->commandBus = $this->prophesize(CommandBusInterface::class);
        $this->tenantDatabaseService = $this->prophesize(DatabaseServiceInterface::class);

        $context = new Context();

        $context->setLoggedUser(new LoggedUser(
            'string_id',
            'user@used.trash',
        ));

        $doctrine = $this->prophesize(ManagerRegistry::class);
        $connection = $this->prophesize(Connection::class);

        $doctrine->getConnection('default')->willReturn($connection->reveal());

        $this->sut = new CreateTenantDatabaseMessageHandler(
            $this->findsTenant->reveal(),
            $this->commandBus->reveal(),
            $this->tenantDatabaseService->reveal(),
            $context,
            $doctrine->reveal(),
        );
    }
}
