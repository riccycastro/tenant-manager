<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\Exception;

use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Ship\Core\Infrastructure\Exception\NonUniqueResultException
 */
final class NonUniqueResultExceptionTest extends TestCase
{
    public function testItIsException(): void
    {
        $sut = new NonUniqueResultException();

        self::assertInstanceOf(\Exception::class, $sut);
    }
}
