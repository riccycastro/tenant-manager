<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Query;

use App\Containers\TenantContainer\Domain\Query\FindTenantsListQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Query\QueryInterface;
use PHPUnit\Framework\TestCase;

final class FindTenantsListQueryTest extends TestCase
{
    public function testItIsQuery(): void
    {
        $sut = new FindTenantsListQuery();

        self::assertInstanceOf(QueryInterface::class, $sut);
    }

    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(FindTenantsListQuery::class);

        self::assertTrue($sut->getProperty('code')->isReadOnly());
        self::assertTrue($sut->getProperty('page')->isReadOnly());
        self::assertTrue($sut->getProperty('itemsPerPage')->isReadOnly());
    }

    public function testItWontChangeTheValuesWhenConstructing(): void
    {
        $code = TenantCode::fromString('a_a_s');
        $sut = new FindTenantsListQuery($code, 3, 5);

        self::assertEquals($code, $sut->code);
        self::assertEquals(3, $sut->page);
        self::assertEquals(5, $sut->itemsPerPage);
    }
}
