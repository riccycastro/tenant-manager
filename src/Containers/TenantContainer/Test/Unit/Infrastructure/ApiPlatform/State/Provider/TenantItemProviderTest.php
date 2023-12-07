<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Get;
use ApiPlatform\State\ProviderInterface;
use App\Containers\TenantContainer\Application\FindsTenantInterface;
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
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider\TenantItemProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider\TenantItemProvider
 *
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
final class TenantItemProviderTest extends TestCase
{
    use ProphecyTrait;

    private TenantItemProvider $sut;

    private ObjectProphecy|FindsTenantInterface $findsTenant;

    public function testItIsProviderInterface(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->sut);
    }

    public function testItReturnsExpectedResult(): void
    {
        $this->findsTenant
            ->withCode(Argument::type(TenantCode::class))
            ->shouldBeCalledOnce()
            ->willReturn($this->findsTenant)
        ;

        $this->findsTenant
            ->getResult()
            ->shouldBeCalledOnce()
            ->willReturn($this->generateTenant())
        ;

        $uriVariables = [
            'code' => 'Kodex',
        ];

        $tenantResource = $this->sut->provide(new Get(), $uriVariables);

        self::assertInstanceOf(TenantOutputDto::class, $tenantResource);
    }

    private function generateTenant(): Tenant
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
            TenantStatus::READY,
            true,
            [],
        );
    }

    protected function setUp(): void
    {
        $this->findsTenant = $this->prophesize(FindsTenantInterface::class);

        $this->sut = new TenantItemProvider(
            $this->findsTenant->reveal(),
        );
    }
}
