<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\TenantOutputDto;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\CreateTenantProcessor;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Application\FindsLoggedUserInterface;
use App\Ship\Core\Domain\Model\LoggedUser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\CreateTenantProcessor
 *
 * @uses \App\Containers\TenantContainer\Domain\Command\CreateTenantCommand
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 * @uses \App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource
 * @uses \App\Ship\Core\Domain\Model\LoggedUser
 */
final class CreateTenantProcessorTest extends TestCase
{
    use ProphecyTrait;

    private CreateTenantProcessor $sut;
    private ObjectProphecy|CommandBusInterface $commandBus;
    private ObjectProphecy|FindsLoggedUserInterface $findsLoggedUser;

    public function testItIsProcessorInterface(): void
    {
        self::assertInstanceOf(ProcessorInterface::class, $this->sut);
    }

    public function testProcessDispatchCreateTenantCommand(): void
    {
        $loggedUser = new LoggedUser(
            '11e01269-5bc2-45cc-b11d-5930f78c3edf',
            'user@site.com'
        );

        $this->findsLoggedUser
            ->getLoggedUser()
            ->shouldBeCalledOnce()
            ->willReturn($loggedUser)
        ;

        $id = TenantId::fromString('1008aa61-7b40-4352-8f44-9acfbc621927');
        $code = TenantCode::fromString('aCode');
        $name = TenantName::fromString('aName');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('0efc7697-abae-42cb-b05c-9aec3efbbadb'),
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
            [],
        );

        $this->commandBus
            ->dispatch(Argument::type(CreateTenantCommand::class))
            ->shouldBeCalledOnce()
            ->willReturn(
                $tenant,
            )
        ;

        $tenantResource = new TenantResource(
            name: 'tenant_name',
            code: 'a_code',
            domainEmail: '@tenant.com'
        );

        $operation = $this->prophesize(Operation::class)->reveal();

        $result = $this->sut->process($tenantResource, $operation);

        self::assertInstanceOf(TenantOutputDto::class, $result);
    }

    protected function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBusInterface::class);
        $this->findsLoggedUser = $this->prophesize(FindsLoggedUserInterface::class);

        $this->sut = new CreateTenantProcessor(
            $this->commandBus->reveal(),
            $this->findsLoggedUser->reveal(),
        );
    }
}
