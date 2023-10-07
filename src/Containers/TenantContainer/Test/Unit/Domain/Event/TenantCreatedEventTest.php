<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Event;

use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent
 *
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 */
final class TenantCreatedEventTest extends TestCase
{
    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(TenantCreatedEvent::class);

        self::assertTrue($sut->getProperty('tenantCode')->isReadOnly());
    }

    public function testItCanBeConstructed(): void
    {
        $sut = new TenantCreatedEvent(
            TenantCode::fromString('a_Code'),
        );

        self::assertInstanceOf(TenantCreatedEvent::class, $sut);
    }
}
