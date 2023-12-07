<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\Query\FindTenantsListQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider\TenantCollectionProvider;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use App\Ship\Core\Domain\Repository\Dto\ModelList;
use App\Ship\Core\Infrastructure\ApiPlatform\State\Paginator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider\TenantCollectionProvider
 *
 * @uses   \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses   \App\Containers\TenantContainer\Domain\Model\User
 * @uses   \App\Containers\TenantContainer\Domain\Query\FindTenantsListQuery
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserId
 * @uses   \App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource
 * @uses   \App\Ship\Core\Domain\Repository\Dto\ModelList
 * @uses   \App\Ship\Core\Infrastructure\ApiPlatform\State\Paginator
 */
final class TenantCollectionProviderTest extends TestCase
{
    use ProphecyTrait;

    private TenantCollectionProvider $sut;
    private ObjectProphecy|QueryBusInterface $queryBus;
    private ObjectProphecy|Pagination $pagination;

    public function testItIsProviderInterface(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->sut);
    }

    public function testItThrowsExceptionIfOperationIsNotCollectionOperationInterface(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This provider should be use with GetCollection operation');

        $this->sut->provide(
            new class() extends Operation {
            },
        );
    }

    public function testQueryAskIsCalled(): void
    {
        $operation = new GetCollection();

        $context = [
            'filters' => ['code' => 'the_code'],
        ];

        $self = $this;

        $this->queryBus
            ->ask(Argument::type(FindTenantsListQuery::class))
            ->should(function (array $data) use ($self) {
                /** @var Call $call */
                $call = $data[0];

                /** @var FindTenantsListQuery $query */
                $query = $call->getArguments()[0];
                $self->assertTrue(
                    $query->code->isEqual(TenantCode::fromString('the_code'))
                );
            })
            ->willReturn(
                new ModelList(
                    [$this->generateTenant(), $this->generateTenant()],
                    10,
                )
            )
        ;

        $result = $this->sut->provide(operation: $operation, context: $context);

        self::assertInstanceOf(Paginator::class, $result);
        self::assertCount(2, $result->getIterator());
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
        $this->queryBus = $this->prophesize(QueryBusInterface::class);
        $this->pagination = new Pagination();

        $this->sut = new TenantCollectionProvider(
            $this->queryBus->reveal(),
            $this->pagination,
        );
    }
}
