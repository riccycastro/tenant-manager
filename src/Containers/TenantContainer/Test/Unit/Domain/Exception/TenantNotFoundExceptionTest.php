<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Exception;

use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TenantNotFoundExceptionTest extends TestCase
{
    public function testItIsConflictHttpException(): void
    {
        $sut = new TenantNotFoundException();

        self::assertInstanceOf(NotFoundHttpException::class, $sut);
    }

    public function testItCanBeCreatedFromTenantCode(): void
    {
        $sut = TenantNotFoundException::fromTenantCode(TenantCode::fromString('akaC_code'));

        self::assertInstanceOf(NotFoundHttpException::class, $sut);
        self::assertEquals('Tenant with code akaC_code not found', $sut->getMessage());
    }
}
