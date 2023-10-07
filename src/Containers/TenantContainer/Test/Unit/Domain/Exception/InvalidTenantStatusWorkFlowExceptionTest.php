<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Exception;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Exception\InvalidTenantStatusWorkFlowException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @covers \App\Containers\TenantContainer\Domain\Exception\InvalidTenantStatusWorkFlowException
 */
final class InvalidTenantStatusWorkFlowExceptionTest extends TestCase
{
    public function testItIsBadRequestHttpException(): void
    {
        $sut = new InvalidTenantStatusWorkFlowException();

        self::assertInstanceOf(BadRequestHttpException::class, $sut);
    }

    public function testItCanBeCreatedFromTenantStatusWorkflow(): void
    {
        $sut = InvalidTenantStatusWorkFlowException::fromTenantStatusWorkflow(
            TenantStatus::READY,
            TenantStatus::WAITING_PROVISIONING,
            [
                TenantStatus::PROVISIONING,
                TenantStatus::READY_FOR_MIGRATION,
            ]
        );

        self::assertInstanceOf(BadRequestHttpException::class, $sut);
        self::assertEquals(
            'ready status is not a valid next status from waiting_provisioning, expected status are provisioning, ready_for_migration',
            $sut->getMessage(),
        );
    }
}
