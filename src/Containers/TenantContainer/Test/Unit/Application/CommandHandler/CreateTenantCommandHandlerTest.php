<?php

namespace App\Containers\TenantContainer\Application\CommandHandler;

use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
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
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CreateTenantCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    private CreateTenantCommandHandler $sut;

    private ObjectProphecy|PersistsTenantInterface $persistsTenant;

    public function testItIsCommandHandler(): void
    {
        self::assertInstanceOf(CommandHandlerInterface::class, $this->sut);
    }

    public function testItHandlesTenantCreate(): void
    {
        $id = TenantId::fromString('1008aa61-7b40-4352-8f44-9acfbc621927');
        $code = TenantCode::fromString('aCode');
        $name = TenantName::fromString('aName');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('0efc7697-abae-42cb-b05c-9aec3efbbadb'),
            UserEmail::fromString('user@tenant.com')
        );

        $tenant = new Tenant(
            $id, $name, $code, $domainEmail, $user
        );

        $createTenantCommand = new CreateTenantCommand(
            id: $id,
            name: $name,
            code: $code,
            domainEmail: $domainEmail,
            user: $user
        );

        $this->persistsTenant
            ->saveAsNew(Argument::type(Tenant::class))
            ->willReturn($tenant)
            ->shouldBeCalled();

        $result = ($this->sut)($createTenantCommand);

        self::assertInstanceOf(Tenant::class, $result);
    }

    protected function setUp(): void
    {
        $this->persistsTenant = $this->prophesize(PersistsTenantInterface::class);

        $this->sut = new CreateTenantCommandHandler($this->persistsTenant->reveal());
    }
}
