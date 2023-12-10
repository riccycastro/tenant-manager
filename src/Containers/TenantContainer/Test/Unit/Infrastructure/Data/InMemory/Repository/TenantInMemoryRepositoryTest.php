<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\Data\InMemory\Repository;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\Model\NewTenant;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\Data\InMemory\Repository\TenantInMemoryRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\Data\InMemory\Repository\TenantInMemoryRepository
 *
 * @uses   \App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent
 * @uses   \App\Containers\TenantContainer\Domain\Model\NewTenant
 * @uses   \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses   \App\Containers\TenantContainer\Domain\Model\User
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserId
 * @uses   \App\Ship\Core\Infrastructure\Data\InMemory\InMemoryRepository
 */
final class TenantInMemoryRepositoryTest extends TestCase
{
    use ProphecyTrait;

    private TenantInMemoryRepository $sut;
    private ObjectProphecy|EventDispatcherInterface $eventDispatcher;

    public function testSaveAsNewProduceExpectedResult(): void
    {
        $newTenant = new NewTenant(
            TenantId::fromString('3aa07c99-4aba-47fa-a6f1-b35c57517ed4'),
            TenantName::fromString('The Tenant Name'),
            TenantCode::fromString('kodixis'),
            TenantDomainEmail::fromString('@string.cv'),
            new User(
                UserId::fromString('134b9725-a611-4985-b124-1c0bdc5cb025'),
                UserEmail::fromString('user@platform.com'),
            ),
        );

        $this->eventDispatcher
            ->dispatch(Argument::type(TenantCreatedEvent::class))
            ->shouldBeCalledOnce()
        ;

        $result = $this->sut->saveAsNew($newTenant);

        self::assertInstanceOf(Tenant::class, $result);

        self::assertEquals(
            [
                'id' => '3aa07c99-4aba-47fa-a6f1-b35c57517ed4',
                'name' => 'The Tenant Name',
                'code' => 'kodixis',
                'domainEmail' => '@string.cv',
                'createdBy' => [
                    'id' => '134b9725-a611-4985-b124-1c0bdc5cb025',
                    'email' => 'user@platform.com',
                ],
                'status' => TenantStatus::WAITING_PROVISIONING->value,
                'isActive' => true,
                'properties' => [],
            ],
            $result->toArray(),
        );
    }

    public function testWithCodeFiltersTenantList(): void
    {
        $this->eventDispatcher
            ->dispatch(Argument::type(TenantCreatedEvent::class))
            ->shouldBeCalled()
        ;

        $newTenant1 = $this->generateNewTenant(TenantCode::fromString('kodex1'));
        $newTenant2 = $this->generateNewTenant(TenantCode::fromString('kodex2'));
        $newTenant3 = $this->generateNewTenant(TenantCode::fromString('kodex3'));

        $this->sut->saveAsNew($newTenant1);
        $this->sut->saveAsNew($newTenant2);
        $this->sut->saveAsNew($newTenant3);

        $result = $this->sut->withCode(TenantCode::fromString('kodex2'))->getResult();

        self::assertEquals($newTenant2->toTenant(), $result);
    }

    private function generateNewTenant(TenantCode $code): NewTenant
    {
        return new NewTenant(
            TenantId::create(),
            TenantName::fromString('The Tenant Name'),
            $code,
            TenantDomainEmail::fromString('@string.cv'),
            new User(
                UserId::create(),
                UserEmail::fromString('user@platform.com'),
            ),
        );
    }

    public function testSave(): void
    {
        $this->eventDispatcher
            ->dispatch(Argument::type(TenantCreatedEvent::class))
            ->shouldBeCalled()
        ;

        $newTenant = $this->generateNewTenant(
            TenantCode::fromString('x_x_x'),
        );

        $this->sut->saveAsNew($newTenant);

        $tenant = $newTenant->toTenant();

        $tenant = $tenant->setStatus(
            status: TenantStatus::PROVISIONING,
        );

        $persistedTenant = $this->sut
            ->save($tenant)
        ;

        self::assertEquals(
            $tenant->getStatus(),
            $persistedTenant->getStatus(),
        );
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $this->sut = new TenantInMemoryRepository(
            $this->eventDispatcher->reveal(),
        );
    }
}
