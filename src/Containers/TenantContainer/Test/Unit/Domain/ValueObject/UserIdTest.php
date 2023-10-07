<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\Exception\InvalidUserIdValueException;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\ValueObject\UserId
 *
 * @uses   \App\Containers\TenantContainer\Domain\Exception\InvalidUserIdValueException
 */
final class UserIdTest extends TestCase
{
    public function testItCanBeCreatedWithoutValue(): void
    {
        $sut = UserId::create();

        self::assertInstanceOf(UserId::class, $sut);
    }

    public function testItCanBeCreatedFromString(): void
    {
        $sut = UserId::fromString('3e4d8876-9039-4e6f-bf00-4797d8f782af');

        self::assertInstanceOf(UserId::class, $sut);
    }

    public function testItFailsOnInvalidUuid(): void
    {
        $this->expectException(InvalidUserIdValueException::class);

        UserId::fromString('notValidUuid');
    }

    public function testItCanBeConverterToString(): void
    {
        $sut = UserId::fromString('a81a390c-d586-4af9-ae20-d56346edf26d');

        self::assertEquals('a81a390c-d586-4af9-ae20-d56346edf26d', $sut->toString());
    }
}
