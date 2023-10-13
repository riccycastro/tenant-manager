<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\Exception;

use App\Ship\Core\Infrastructure\Exception\NoResultException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Ship\Core\Infrastructure\Exception\NoResultException
 */
final class NoResultExceptionTest extends TestCase
{
    public function testItIsException(): void
    {
        $sut = new NoResultException();

        self::assertInstanceOf(\Exception::class, $sut);
    }
}
