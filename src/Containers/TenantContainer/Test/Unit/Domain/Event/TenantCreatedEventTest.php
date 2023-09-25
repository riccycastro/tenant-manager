<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Event;

use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use PHPUnit\Framework\TestCase;

final class TenantCreatedEventTest extends TestCase
{
    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(TenantCreatedEvent::class);

        self::assertTrue($sut->getProperty('tenantCode')->isReadOnly());
    }
}
