<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Exception;

use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @covers \App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException
 *
 * @uses   \App\Containers\TenantContainer\Domain\Query\FindTenantQuery
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 */
final class TenantNotFoundExceptionTest extends TestCase
{
    public function testItIsNotFoundException(): void
    {
        $sut = new TenantNotFoundException();

        self::assertInstanceOf(NotFoundHttpException::class, $sut);
    }

    public function testItCanBeCreatedFromTenantCode(): void
    {
        $sut = TenantNotFoundException::fromTenantCode(TenantCode::fromString('akaC_code'));

        self::assertInstanceOf(TenantNotFoundException::class, $sut);
        self::assertEquals('Tenant with code akaC_code not found', $sut->getMessage());
    }

    public function testItCanBeCreatedFromFindTenantQuery(): void
    {
        $sut = TenantNotFoundException::fromFindTenantQuery(
            new FindTenantQuery(),
        );

        self::assertInstanceOf(TenantNotFoundException::class, $sut);
    }

    public function testItContainsTenantCodeIfProvidedWhenCreatedFromFindTenantQuery(): void
    {
        $sut = TenantNotFoundException::fromFindTenantQuery(
            new FindTenantQuery(
                TenantCode::fromString('a_code'),
            ),
        );

        self::assertInstanceOf(TenantNotFoundException::class, $sut);
        self::assertStringContainsString('a_code', $sut->getMessage());
    }
}
