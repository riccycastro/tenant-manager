<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\Exception\InvalidTenantIdValueException;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 *
 * @uses \App\Containers\TenantContainer\Domain\Exception\InvalidTenantIdValueException
 */
final class TenantIdTest extends TestCase
{
    public function testItCanBeCreatedWithoutValue(): void
    {
        $sut = TenantId::create();

        self::assertInstanceOf(TenantId::class, $sut);
    }

    public function testItCanBeCreatedFromString(): void
    {
        $sut = TenantId::fromString('64192698-bb1e-4d2a-ad51-810e1e96a347');

        self::assertInstanceOf(TenantId::class, $sut);
    }

    public function testItFailsOnInvalidUuid(): void
    {
        $this->expectException(InvalidTenantIdValueException::class);

        TenantId::fromString('notValidUuid');
    }

    public function testItCanBeConverterToString(): void
    {
        $sut = TenantId::fromString('64192698-bb1e-4d2a-ad51-810e1e96a347');

        self::assertEquals('64192698-bb1e-4d2a-ad51-810e1e96a347', $sut->toString());
    }
}
