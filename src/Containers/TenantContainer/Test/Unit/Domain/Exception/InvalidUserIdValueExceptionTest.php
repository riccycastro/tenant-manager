<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Exception;

use App\Containers\TenantContainer\Domain\Exception\InvalidUserIdValueException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Exception\InvalidUserIdValueException
 */
final class InvalidUserIdValueExceptionTest extends TestCase
{
    public function testItIsConflictHttpException(): void
    {
        $sut = new InvalidUserIdValueException();

        self::assertInstanceOf(\InvalidArgumentException::class, $sut);
    }

    public function testItCanBeCreatedFromValue(): void
    {
        $sut = InvalidUserIdValueException::fromValue('invalidUserId');

        self::assertInstanceOf(\InvalidArgumentException::class, $sut);
        self::assertEquals('Value `invalidUserId` is not a valid user unique identifier', $sut->getMessage());
    }
}
