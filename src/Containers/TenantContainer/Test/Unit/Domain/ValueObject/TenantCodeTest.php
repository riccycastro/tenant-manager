<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @covers \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 */
final class TenantCodeTest extends TestCase
{
    public function testItCanBeCreatedFromString(): void
    {
        $sut = TenantCode::fromString('code45');

        self::assertInstanceOf(TenantCode::class, $sut);
    }

    public function testItFailsOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantCode::fromString('');
    }

    public function testItFailsIfCodeContainsWhiteSpace(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantCode::fromString('white space');
    }

    public function testItFailsIfCodeContainsSpecialChar(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantCode::fromString('sp€ci@al*');
    }

    public function testTwoTenantCodesWithSameCodeAreEqual(): void
    {
        $sut = TenantCode::fromString('code-45');
        $another = TenantCode::fromString('code-45');

        self::assertTrue($sut->isEqual($another));
    }

    public function testTwoTenantCodesWithDifferentCodeArentEqual(): void
    {
        $sut = TenantCode::fromString('code-45');
        $another = TenantCode::fromString('code-45s');

        self::assertFalse($sut->isEqual($another));
    }

    public function testItCanBeConvertedToString(): void
    {
        $sut = TenantCode::fromString('code-45s');

        self::assertEquals('code-45s', $sut->toString());
    }
}
