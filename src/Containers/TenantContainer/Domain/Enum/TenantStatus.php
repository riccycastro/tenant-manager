<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Enum;

enum TenantStatus: string
{
    case WAITING_PROVISIONING = 'waiting_provisioning';
    case PROVISIONING = 'provisioning';
    case READY_FOR_MIGRATION = 'ready_for_migration';
    case READY = 'ready';
    case DEACTIVATED = 'deactivated';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
