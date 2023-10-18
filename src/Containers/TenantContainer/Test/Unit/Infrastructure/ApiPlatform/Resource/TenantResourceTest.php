<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\ApiPlatform\Resource;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource
 *
 * @uses \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class TenantResourceTest extends TestCase
{
    public function testItCanBeCreatedFromModel(): void
    {
        $id = TenantId::fromString('bf2ec8bf-68f3-498a-846e-0f503fe05e41');
        $code = TenantCode::fromString('aCode');
        $name = TenantName::fromString('aName');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('4680bbce-228d-4340-8efb-3d3eff40602f'),
            UserEmail::fromString('user@tenant.com')
        );

        $tenant = new Tenant(
            id: $id,
            name: $name,
            code: $code,
            domainEmail: $domainEmail,
            createdBy: $user,
            status: TenantStatus::WAITING_PROVISIONING,
            isActive: true
        );

        $sut = TenantResource::fromModel($tenant);

        self::assertInstanceOf(TenantResource::class, $sut);
        self::assertEquals('aName', $sut->name);
        self::assertEquals('aCode', $sut->code);
        self::assertTrue($sut->isActive);
        self::assertEquals(TenantStatus::WAITING_PROVISIONING->value, $sut->status);
        self::assertEquals('@tenant.com', $sut->domainEmail);
    }
}
