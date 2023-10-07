<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Exception;

use App\Containers\TenantContainer\Domain\Exception\TenantCodeAlreadyExistException;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @covers \App\Containers\TenantContainer\Domain\Exception\TenantCodeAlreadyExistException
 *
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 */
final class TenantCodeAlreadyExistExceptionTest extends TestCase
{
    public function testItIsConflictHttpException(): void
    {
        $sut = new TenantCodeAlreadyExistException();

        self::assertInstanceOf(ConflictHttpException::class, $sut);
    }

    public function testItCanBeCreatedFromCode(): void
    {
        $sut = TenantCodeAlreadyExistException::fromCode(TenantCode::fromString('akaC_code'));

        self::assertInstanceOf(ConflictHttpException::class, $sut);
        self::assertEquals('Tenant with code akaC_code already exists', $sut->getMessage());
    }
}
