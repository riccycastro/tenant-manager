<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Exception;

use App\Containers\TenantContainer\Domain\Exception\UserNotFoundException;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @covers \App\Containers\TenantContainer\Domain\Exception\UserNotFoundException
 *
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class UserNotFoundExceptionTest extends TestCase
{
    public function testItIsNotFoundHttpException(): void
    {
        $sut = new UserNotFoundException();

        self::assertInstanceOf(NotFoundHttpException::class, $sut);
    }

    public function testItCanBeCreatedFromUserId(): void
    {
        $sut = UserNotFoundException::fromUserId(
            UserId::create(),
        );

        self::assertInstanceOf(UserNotFoundException::class, $sut);
    }
}
