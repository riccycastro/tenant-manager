<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\Data\Doctrine\Entity;

use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Test\Unit\Infrastructure\Components\EntityInstantiator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\UserEntity
 *
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class UserEntityTest extends TestCase
{
    use EntityInstantiator;

    private UserEntity $sut;

    public function testItCanBeConvertedToUser(): void
    {
        $result = $this->sut->toUser();

        self::assertEquals(
            [
                'id' => 'bdb08e76-14db-4cc4-abbb-b01a2b6f5f0a',
                'email' => 'user@site.com',
            ],
            $result->toArray(),
        );
    }

    protected function setUp(): void
    {
        $this->sut = $this->instantiateEntity(
            UserEntity::class,
            [
                'id' => 'bdb08e76-14db-4cc4-abbb-b01a2b6f5f0a',
                'email' => 'user@site.com',
            ],
        );
    }
}
