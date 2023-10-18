<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
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

        $this->commandBus
            ->dispatch(Argument::type(CreateTenantCommand::class))
            ->shouldBeCalledOnce()
        ;

        $tenantResource = new TenantResource(
            name: 'tenant_name',
            code: 'a_code',
            domainEmail: '@tenant.com'
        );

        $operation = $this->prophesize(Operation::class)->reveal();

        $this->sut->process($tenantResource, $operation);
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
