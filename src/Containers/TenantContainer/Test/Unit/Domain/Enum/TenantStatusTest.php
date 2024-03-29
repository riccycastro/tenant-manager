<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Enum;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Enum\TenantStatus
 */
final class TenantStatusTest extends TestCase
{
    /**
     * @dataProvider tenantStatusToStringProvider
     */
    public function testTheStringValueIsAsExpected(TenantStatus $expectedStatus, string $input): void
    {
        $inputStatus = TenantStatus::from($input);

        self::assertEquals($expectedStatus, $inputStatus);
    }

    public function testValuesMethodReturnsExpectedResult(): void
    {
        $result = TenantStatus::values();

        self::assertEquals([
            'waiting_provisioning',
            'provisioning',
            'ready_for_migration',
            'ready',
            'deactivated',
        ],
            $result,
        );
    }

    public function tenantStatusToStringProvider(): \Generator
    {
        yield [
            TenantStatus::WAITING_PROVISIONING,
            'waiting_provisioning',
        ];
        yield [
            TenantStatus::PROVISIONING,
            'provisioning',
        ];
        yield [
            TenantStatus::READY_FOR_MIGRATION,
            'ready_for_migration',
        ];
        yield [
            TenantStatus::READY,
            'ready',
        ];
        yield [
            TenantStatus::DEACTIVATED,
            'deactivated',
        ];
    }
}
