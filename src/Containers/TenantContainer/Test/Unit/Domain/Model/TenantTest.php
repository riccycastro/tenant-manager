<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Model;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Exception\InvalidTenantStatusWorkFlowException;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

final class TenantTest extends TestCase
{
    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(Tenant::class);

        self::assertTrue($sut->getProperty('id')->isReadOnly());
        self::assertTrue($sut->getProperty('name')->isReadOnly());
        self::assertTrue($sut->getProperty('code')->isReadOnly());
        self::assertTrue($sut->getProperty('domainEmail')->isReadOnly());
        self::assertTrue($sut->getProperty('createdBy')->isReadOnly());
        self::assertTrue($sut->getProperty('status')->isReadOnly());
        self::assertTrue($sut->getProperty('isActive')->isReadOnly());
    }

    public function testHasSameCodeValidatesRight(): void
    {
        $sut = $this->generateSubjectUnderTest();

        self::assertTrue($sut->hasSameCode(TenantCode::fromString('aCode')));
        self::assertFalse($sut->hasSameCode(TenantCode::fromString('phake')));
    }

    private function generateSubjectUnderTest(TenantStatus $status = TenantStatus::WAITING_PROVISIONING): Tenant
    {
        $id = TenantId::fromString('bf2ec8bf-68f3-498a-846e-0f503fe05e41');
        $code = TenantCode::fromString('aCode');
        $name = TenantName::fromString('aName');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('4680bbce-228d-4340-8efb-3d3eff40602f'),
            UserEmail::fromString('user@tenant.com')
        );

        return new Tenant(
            $id,
            $name,
            $code,
            $domainEmail,
            $user,
            $status,
            false,
        );
    }

    public function testToArrayReturnsExpectedResult(): void
    {
        $sut = $this->generateSubjectUnderTest();

        $result = $sut->toArray();

        self::assertEquals(
            [
                'id' => 'bf2ec8bf-68f3-498a-846e-0f503fe05e41',
                'name' => 'aName',
                'code' => 'aCode',
                'domainEmail' => '@tenant.com',
                'createdBy' => [
                    'id' => '4680bbce-228d-4340-8efb-3d3eff40602f',
                    'email' => 'user@tenant.com',
                ],
                'status' => 'waiting_provisioning',
                'isActive' => false,
            ],
            $result
        );
    }

    /**
     * @dataProvider tenantStatusProvider
     */
    public function testTenantStatusWorkflow(TenantStatus $currentStatus, TenantStatus $nextStatus, bool $shouldThrowException): void
    {
        if ($shouldThrowException) {
            $this->expectException(InvalidTenantStatusWorkFlowException::class);
        }

        $sut = $this->generateSubjectUnderTest($currentStatus);

        $result = $sut->update(status: $nextStatus);

        if (!$shouldThrowException) {
            self::assertEquals($nextStatus, $result->getStatus());
        }
    }

    public function tenantStatusProvider(): \Generator
    {
        yield 'WAITING_PROVISIONING should accept PROVISIONING as next status' => [
            TenantStatus::WAITING_PROVISIONING, TenantStatus::PROVISIONING, false,
        ];
        yield 'WAITING_PROVISIONING should not accept READY_FOR_MIGRATION as next status' => [
            TenantStatus::WAITING_PROVISIONING, TenantStatus::READY_FOR_MIGRATION, true,
        ];
        yield 'WAITING_PROVISIONING should not accept WAITING_PROVISIONING as next status' => [
            TenantStatus::WAITING_PROVISIONING, TenantStatus::WAITING_PROVISIONING, true,
        ];
        yield 'WAITING_PROVISIONING should not accept READY as next status' => [
            TenantStatus::WAITING_PROVISIONING, TenantStatus::READY, true,
        ];
        yield 'WAITING_PROVISIONING should not accept DEACTIVATED as next status' => [
            TenantStatus::WAITING_PROVISIONING, TenantStatus::DEACTIVATED, true,
        ];

        yield 'PROVISIONING should accept WAITING_PROVISIONING as next status' => [
            TenantStatus::PROVISIONING, TenantStatus::WAITING_PROVISIONING, false,
        ];
        yield 'PROVISIONING should accept READY_FOR_MIGRATION as next status' => [
            TenantStatus::PROVISIONING, TenantStatus::READY_FOR_MIGRATION, false,
        ];
        yield 'PROVISIONING should not accept PROVISIONING as next status' => [
            TenantStatus::PROVISIONING, TenantStatus::PROVISIONING, true,
        ];
        yield 'PROVISIONING should not accept READY as next status' => [
            TenantStatus::PROVISIONING, TenantStatus::READY, true,
        ];
        yield 'PROVISIONING should not accept DEACTIVATED as next status' => [
            TenantStatus::PROVISIONING, TenantStatus::DEACTIVATED, true,
        ];

        yield 'READY_FOR_MIGRATION should accept READY as next status' => [
            TenantStatus::READY_FOR_MIGRATION, TenantStatus::READY, false,
        ];
        yield 'READY_FOR_MIGRATION should not accept WAITING_PROVISIONING as next status' => [
            TenantStatus::READY_FOR_MIGRATION, TenantStatus::WAITING_PROVISIONING, true,
        ];
        yield 'READY_FOR_MIGRATION should not accept PROVISIONING as next status' => [
            TenantStatus::READY_FOR_MIGRATION, TenantStatus::PROVISIONING, true,
        ];
        yield 'READY_FOR_MIGRATION should not accept READY_FOR_MIGRATION as next status' => [
            TenantStatus::READY_FOR_MIGRATION, TenantStatus::READY_FOR_MIGRATION, true,
        ];
        yield 'READY_FOR_MIGRATION should not accept DEACTIVATED as next status' => [
            TenantStatus::READY_FOR_MIGRATION, TenantStatus::DEACTIVATED, true,
        ];

        yield 'READY should accept DEACTIVATED as next status' => [
            TenantStatus::READY, TenantStatus::DEACTIVATED, false,
        ];
        yield 'READY should not accept WAITING_PROVISIONING as next status' => [
            TenantStatus::READY, TenantStatus::WAITING_PROVISIONING, true,
        ];
        yield 'READY should not accept PROVISIONING as next status' => [
            TenantStatus::READY, TenantStatus::PROVISIONING, true,
        ];
        yield 'READY should not accept READY_FOR_MIGRATION as next status' => [
            TenantStatus::READY, TenantStatus::READY_FOR_MIGRATION, true,
        ];
        yield 'READY should not accept READY as next status' => [
            TenantStatus::READY, TenantStatus::READY, true,
        ];

        yield 'DEACTIVATED should accept READY as next status' => [
            TenantStatus::DEACTIVATED, TenantStatus::READY, false,
        ];
        yield 'DEACTIVATED should not accept WAITING_PROVISIONING as next status' => [
            TenantStatus::DEACTIVATED, TenantStatus::WAITING_PROVISIONING, true,
        ];
        yield 'DEACTIVATED should not accept PROVISIONING as next status' => [
            TenantStatus::DEACTIVATED, TenantStatus::PROVISIONING, true,
        ];
        yield 'DEACTIVATED should not accept READY_FOR_MIGRATION as next status' => [
            TenantStatus::DEACTIVATED, TenantStatus::READY_FOR_MIGRATION, true,
        ];
        yield 'DEACTIVATED should not accept DEACTIVATED as next status' => [
            TenantStatus::DEACTIVATED, TenantStatus::DEACTIVATED, true,
        ];
    }
}
