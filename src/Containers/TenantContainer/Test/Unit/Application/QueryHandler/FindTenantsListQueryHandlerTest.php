<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Application\QueryHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\QueryHandler\FindTenantsListQueryHandler;
use App\Containers\TenantContainer\Domain\Query\FindTenantsListQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Application\QueryHandler\QueryHandlerInterface;
use App\Ship\Core\Domain\Repository\Dto\ModelList;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Application\QueryHandler\FindTenantsListQueryHandler
 *
 * @uses \App\Containers\TenantContainer\Domain\Query\FindTenantsListQuery
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Ship\Core\Domain\Repository\Dto\ModelList
 */
final class FindTenantsListQueryHandlerTest extends TestCase
{
    use ProphecyTrait;

    private FindTenantsListQueryHandler $sut;
    private ObjectProphecy|FindsTenantInterface $findsTenant;

    public function testItIsQueryHandlerInterface(): void
    {
        self::assertInstanceOf(QueryHandlerInterface::class, $this->sut);
    }

    public function testCodeIsUsedOnQueryWhenProvided(): void
    {
        $query = new FindTenantsListQuery(
            TenantCode::fromString('kode_x'),
        );

        $this->findsTenant
            ->withCode($query->code)
            ->shouldBeCalled()
            ->willReturn($this->findsTenant);

        $this->findsTenant
            ->getListResult()
            ->shouldBeCalled()
            ->willReturn(new ModelList([], 0));

        ($this->sut)($query);
    }

    public function testPageAndItemPerPageIsUsedOnQueryWhenProvided(): void
    {
        $query = new FindTenantsListQuery(
            page: 1,
            itemsPerPage: 30,
        );

        $this->findsTenant
            ->withPagination($query->page, $query->itemsPerPage)
            ->shouldBeCalled()
            ->willReturn($this->findsTenant);

        $this->findsTenant
            ->getListResult()
            ->shouldBeCalled()
            ->willReturn(new ModelList([], 0));

        ($this->sut)($query);
    }

    public function testGetListResultIsCalledIfNoFilter(): void
    {
        $query = new FindTenantsListQuery();

        $this->findsTenant
            ->getListResult()
            ->shouldBeCalled()
            ->willReturn(new ModelList([], 0));

        ($this->sut)($query);
    }

    protected function setUp(): void
    {
        $this->findsTenant = $this->prophesize(FindsTenantInterface::class);

        $this->sut = new FindTenantsListQueryHandler(
            $this->findsTenant->reveal(),
        );
    }
}
