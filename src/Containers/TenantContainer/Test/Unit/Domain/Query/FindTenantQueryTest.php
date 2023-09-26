<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Query;

use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Query\QueryInterface;
use PHPUnit\Framework\TestCase;

final class FindTenantQueryTest extends TestCase
{
    public function testItIsQuery(): void
    {
        $sut = new FindTenantQuery(TenantCode::fromString('a_a_s'));

        self::assertInstanceOf(QueryInterface::class, $sut);
    }

    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(FindTenantQuery::class);

        self::assertTrue($sut->getProperty('code')->isReadOnly());
    }

    public function testItWontChangeTheValuesWhenConstructing(): void
    {
        $code = TenantCode::fromString('a_a_s');
        $sut = new FindTenantQuery($code);

        self::assertEquals($code, $sut->code);
    }
}
