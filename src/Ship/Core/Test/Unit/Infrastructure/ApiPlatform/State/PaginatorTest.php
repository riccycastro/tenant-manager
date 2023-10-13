<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\ApiPlatform\State;

use ApiPlatform\State\Pagination\PaginatorInterface;
use App\Ship\Core\Infrastructure\ApiPlatform\State\Paginator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Ship\Core\Infrastructure\ApiPlatform\State\Paginator
 */
final class PaginatorTest extends TestCase
{
    public function testItIsPaginatorInterface(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([]),
            0,
            0,
            0,
            0,
        );

        self::assertInstanceOf(PaginatorInterface::class, $sut);
    }

    public function testItIsIteratorAggregate(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([]),
            0,
            0,
            0,
            0,
        );

        self::assertInstanceOf(\IteratorAggregate::class, $sut);
    }

    public function testCountReturnsExpectedValue(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([1, 2, 3, 4]),
            0,
            0,
            0,
            0,
        );

        self::assertEquals(4, $sut->count());
    }

    public function testGetIteratorReturnsExpectedValue(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([1, 2, 3, 4]),
            0,
            0,
            0,
            0,
        );

        self::assertEquals(
            new \ArrayIterator([1, 2, 3, 4]),
            $sut->getIterator(),
        );
    }

    public function testGetLastPageReturnsExpectedResult(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([1, 2, 3, 4]),
            0,
            0,
            40,
            0,
        );

        self::assertEquals(40, $sut->getLastPage());
    }

    public function testGetTotalItemsReturnsExpectedResult(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([1, 2, 3, 4]),
            0,
            0,
            40,
            76,
        );

        self::assertEquals(76, $sut->getTotalItems());
    }

    public function testGetCurrentPageReturnsExpectedResult(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([1, 2, 3, 4]),
            23,
            0,
            40,
            76,
        );

        self::assertEquals(23, $sut->getCurrentPage());
    }

    public function testGetItemsPerPageReturnsExpectedResult(): void
    {
        $sut = new Paginator(
            new \ArrayIterator([1, 2, 3, 4]),
            23,
            50,
            40,
            76,
        );

        self::assertEquals(50, $sut->getItemsPerPage());
    }
}
