<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Command;

use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Command\CommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand
 *
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 */
final class UpdateTenantCommandTest extends TestCase
{
    public function testItIsCommand(): void
    {
        $sut = new UpdateTenantCommand(
            TenantCode::fromString('akaKode'),
        );

        self::assertInstanceOf(CommandInterface::class, $sut);
    }

    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(UpdateTenantCommand::class);

        self::assertTrue($sut->getProperty('code')->isReadOnly());
        self::assertTrue($sut->getProperty('status')->isReadOnly());
    }

    public function testItWontChangeTheValuesWhenConstructing(): void
    {
        $tenantCode = TenantCode::fromString('akaKode');
        $tenantStatus = TenantStatus::READY_FOR_MIGRATION;

        $sut = new UpdateTenantCommand(
            $tenantCode,
            $tenantStatus,
        );

        self::assertEquals($tenantCode, $sut->code);
        self::assertEquals($tenantStatus, $sut->status);
    }
}
