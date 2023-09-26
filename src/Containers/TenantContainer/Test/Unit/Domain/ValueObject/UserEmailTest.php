<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class UserEmailTest extends TestCase
{
    public function testItCanBeCreatedFromString(): void
    {
        $sut = UserEmail::fromString('user@site.com');

        self::assertInstanceOf(UserEmail::class, $sut);
    }

    public function testItFailsOnInvalidEmailValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value to be a valid e-mail address. Got: "user£Site.com"');

        UserEmail::fromString('user£Site.com');
    }

    public function testItCanBeConvertedToString(): void
    {
        $sut = UserEmail::fromString('user@web.cv');

        self::assertEquals('user@web.cv', $sut->toString());
    }
}
