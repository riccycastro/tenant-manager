<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Application\CommandHandler;

use App\Containers\TenantContainer\Application\CommandHandler\UpdateTenantCommandHandler;
use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\UpdatesTenantInterface;
use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Ship\Core\Application\CommandHandler\CommandHandlerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Application\CommandHandler\UpdateTenantCommandHandler
 *
 * @uses \App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand
 * @uses \App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException
 * @uses \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class UpdateTenantCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    private UpdateTenantCommandHandler $sut;

    private ObjectProphecy|FindsTenantInterface $findsTenant;
    private ObjectProphecy|UpdatesTenantInterface $updatesTenant;

    public function testItIsCommandHandler(): void
    {
        self::assertInstanceOf(CommandHandlerInterface::class, $this->sut);
    }

    public function testItShouldThrowTenantNotFoundExceptionOnTenantNotFound(): void
    {
        $this->expectException(TenantNotFoundException::class);
        $this->expectExceptionMessage('Tenant with code a_kode not found');

        $command = new UpdateTenantCommand(
            code: TenantCode::fromString('a_kode'),
        );

        $this->findsTenant
            ->withCode($command->code)
            ->shouldBeCalled()
            ->willReturn($this->findsTenant);
        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn(null);

        ($this->sut)($command);
    }

    public function testItUpdatesTenant(): void
    {
        $tenantStatus = TenantStatus::PROVISIONING;
        $command = new UpdateTenantCommand(
            code: TenantCode::fromString('a_kode'),
            status: $tenantStatus
        );

        $id = TenantId::fromString('bf2ec8bf-68f3-498a-846e-0f503fe05e41');
        $code = TenantCode::fromString('aCode');
        $name = TenantName::fromString('aName');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('4680bbce-228d-4340-8efb-3d3eff40602f'),
            UserEmail::fromString('user@tenant.com')
        );

        $tenant = new Tenant(
            $id,
            $name,
            $code,
            $domainEmail,
            $user,
            TenantStatus::WAITING_PROVISIONING,
            false,
        );

        $this->findsTenant
            ->withCode($command->code)
            ->shouldBeCalled()
            ->willReturn($this->findsTenant);
        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn($tenant);

        $self = $this;

        $this->updatesTenant
            ->save(Argument::type(Tenant::class))
            ->should(function (array $data) use ($tenantStatus, $self) {
                /** @var Call $call */
                $call = $data[0];

                /** @var Tenant $tenant */
                $tenant = $call->getArguments()[0];
                $self->assertEquals($tenantStatus, $tenant->getStatus());
            })
            ->willReturn($tenant);

        ($this->sut)($command);
    }

    protected function setUp(): void
    {
        $this->findsTenant = $this->prophesize(FindsTenantInterface::class);
        $this->updatesTenant = $this->prophesize(UpdatesTenantInterface::class);

        $this->sut = new UpdateTenantCommandHandler(
            $this->findsTenant->reveal(),
            $this->updatesTenant->reveal(),
        );
    }
}
