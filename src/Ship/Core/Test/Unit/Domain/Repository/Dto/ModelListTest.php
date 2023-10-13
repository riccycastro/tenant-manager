<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Domain\Repository\Dto;

use App\Ship\Core\Domain\Repository\Dto\ModelList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Ship\Core\Domain\Repository\Dto\ModelList
 */
final class ModelListTest extends TestCase
{
    public function testItCanBeInstantiated(): void
    {
        $sut = new ModelList(
            [],
            0,
        );

        self::assertInstanceOf(ModelList::class, $sut);
    }

    public function testPropertiesAreReadonly(): void
    {
        $sut = new \ReflectionClass(ModelList::class);

        self::assertTrue($sut->getProperty('items')->isReadOnly());
        self::assertTrue($sut->getProperty('count')->isReadOnly());
    }
}
