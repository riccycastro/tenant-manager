<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Model;

use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Ship\Core\Domain\Model\LoggedUser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Model\User
 *
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 * @uses \App\Ship\Core\Domain\Model\LoggedUser
 */
final class UserTest extends TestCase
{
    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(User::class);

        self::assertTrue($sut->getProperty('id')->isReadOnly());
        self::assertTrue($sut->getProperty('email')->isReadOnly());
    }

    public function testItCanBeCreatedFromCoreUser(): void
    {
        $sut = User::fromCoreUser(new LoggedUser('672ee1fa-052b-408a-9105-5137c1147935', 'user@tenant.com'));

        self::assertInstanceOf(User::class, $sut);
        self::assertEquals('672ee1fa-052b-408a-9105-5137c1147935', $sut->getId()->toString());
        self::assertEquals('user@tenant.com', $sut->getEmail()->toString());
    }

    public function testToArrayReturnsExpectedResult(): void
    {
        $sut = new User(
            UserId::fromString('2393d04f-5748-475e-af14-67dbda240f34'),
            UserEmail::fromString('user@tenant.com')
        );

        $result = $sut->toArray();

        self::assertEquals(
            [
                'id' => '2393d04f-5748-475e-af14-67dbda240f34',
                'email' => 'user@tenant.com',
            ],
            $result,
        );
    }
}
