<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @covers \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 */
final class TenantNameTest extends TestCase
{
    public function testItCanBeCreatedFromString(): void
    {
        $sut = TenantName::fromString('Web Platform');

        self::assertInstanceOf(TenantName::class, $sut);
    }

    public function testItFailsOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantName::fromString('');
    }

    public function testItFailsOnInvalidMaximumRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantName::fromString('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,.');
    }

    public function testItCanBeConvertedToString(): void
    {
        $sut = TenantName::fromString('website');

        self::assertEquals('website', $sut->toString());
    }
}
