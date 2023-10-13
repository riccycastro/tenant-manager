<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Domain\Model;

use App\Ship\Core\Domain\Model\LoggedUser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Ship\Core\Domain\Model\LoggedUser
 */
final class LoggedUserTest extends TestCase
{
    public function testItExposeId(): void
    {
        $sut = new LoggedUser(
            '97cd4cd3-0f1a-40fd-a513-5c6ba6f4a3ad',
            'user@site.com',
        );

        self::assertEquals('97cd4cd3-0f1a-40fd-a513-5c6ba6f4a3ad', $sut->getId());
    }

    public function testItExposeEmail(): void
    {
        $sut = new LoggedUser(
            '97cd4cd3-0f1a-40fd-a513-5c6ba6f4a3ad',
            'user@site.com',
        );

        self::assertEquals('user@site.com', $sut->getEmail());
    }
}
