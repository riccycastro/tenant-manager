<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Exception;

use App\Containers\TenantContainer\Domain\Exception\InvalidTenantIdValueException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Exception\InvalidTenantIdValueException
 */
final class InvalidTenantIdValueExceptionTest extends TestCase
{
    public function testItIsInvalidArgumentException(): void
    {
        $sut = new InvalidTenantIdValueException();

        self::assertInstanceOf(\InvalidArgumentException::class, $sut);
    }

    public function testItCanBeCreatedFromValue(): void
    {
        $sut = InvalidTenantIdValueException::fromValue('invalidTenantIdValue');

        self::assertInstanceOf(\InvalidArgumentException::class, $sut);
        self::assertEquals(
            'Value `invalidTenantIdValue` is not a valid tenant unique identifier',
            $sut->getMessage(),
        );
    }
}
