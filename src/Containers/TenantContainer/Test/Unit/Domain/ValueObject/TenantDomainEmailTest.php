<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class TenantDomainEmailTest extends TestCase
{
    public function testItCanBeCreatedFromString(): void
    {
        $sut = TenantDomainEmail::fromString('@tenant.com');

        self::assertInstanceOf(TenantDomainEmail::class, $sut);
    }

    public function testItFailsOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantDomainEmail::fromString('');
    }

    public function testItFailsIfMalformedDomainEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantDomainEmail::fromString('tenant.com');
    }

    public function testItCanBeConvertedToString(): void
    {
        $sut = TenantDomainEmail::fromString('@site.com');

        self::assertEquals('@site.com', $sut->toString());
    }
}
