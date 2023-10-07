<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Application\QueryHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\QueryHandler\FindTenantQueryHandler;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Ship\Core\Application\QueryHandler\QueryHandlerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Application\QueryHandler\FindTenantQueryHandler
 *
 * @uses \App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException
 * @uses \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\Query\FindTenantQuery
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class FindTenantQueryHandlerTest extends TestCase
{
    use ProphecyTrait;

    private FindTenantQueryHandler $sut;

    private ObjectProphecy|FindsTenantInterface $findsTenant;

    public function testItIsQueryHandlerInterface(): void
    {
        self::assertInstanceOf(QueryHandlerInterface::class, $this->sut);
    }

    public function testItReturnsFoundedTenant(): void
    {
        $tenant = $this->generateTenant();

        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn($tenant);

        $result = ($this->sut)(new FindTenantQuery());

        self::assertInstanceOf(Tenant::class, $result);
        $result->hasSameCode(TenantCode::fromString('aCode'));
    }

    private function generateTenant(): Tenant
    {
        return new Tenant(
            TenantId::fromString('bf2ec8bf-68f3-498a-846e-0f503fe05e41'),
            TenantName::fromString('aName'),
            TenantCode::fromString('aCode'),
            TenantDomainEmail::fromString('@tenant.com'),
            new User(
                UserId::fromString('4680bbce-228d-4340-8efb-3d3eff40602f'),
                UserEmail::fromString('user@tenant.com')
            ),
            TenantStatus::WAITING_PROVISIONING,
            false,
        );
    }

    public function testCodeIsUsedOnQueryWhenProvided(): void
    {
        $tenant = $this->generateTenant();

        $query = new FindTenantQuery(code: TenantCode::fromString('aCode'));

        $this->findsTenant
            ->withCode($query->code)
            ->shouldBeCalled()
            ->willReturn($this->findsTenant);

        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn($tenant);

        $result = ($this->sut)($query);

        self::assertInstanceOf(Tenant::class, $result);
        $result->hasSameCode(TenantCode::fromString('aCode'));
    }

    public function testItShouldThrowTenantNotFoundOnNullReturn(): void
    {
        $this->expectException(TenantNotFoundException::class);

        $this->findsTenant
            ->getResult()
            ->shouldBeCalled()
            ->willReturn(null);

        ($this->sut)(new FindTenantQuery());
    }

    protected function setUp(): void
    {
        $this->findsTenant = $this->prophesize(FindsTenantInterface::class);

        $this->sut = new FindTenantQueryHandler(
            $this->findsTenant->reveal(),
        );
    }
}
