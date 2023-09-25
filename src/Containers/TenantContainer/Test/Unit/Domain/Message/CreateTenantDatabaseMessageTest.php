<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Message;

use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Message\AsyncMessageInterface;
use PHPUnit\Framework\TestCase;

final class CreateTenantDatabaseMessageTest extends TestCase
{
    public function testItIsAsyncMessage(): void
    {
        $sut = new CreateTenantDatabaseMessage(TenantCode::fromString('kodex'));

        self::assertInstanceOf(AsyncMessageInterface::class, $sut);
    }

    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(CreateTenantDatabaseMessage::class);

        self::assertTrue($sut->getProperty('tenantCode')->isReadOnly());
    }
}
