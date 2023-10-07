<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Model;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\Model\NewTenant;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Model\NewTenant
 *
 * @uses \App\Containers\TenantContainer\Domain\Enum\TenantStatus
 * @uses \App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent
 * @uses \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class NewTenantTest extends TestCase
{
    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(NewTenant::class);

        self::assertTrue($sut->getProperty('id')->isReadOnly());
        self::assertTrue($sut->getProperty('name')->isReadOnly());
        self::assertTrue($sut->getProperty('code')->isReadOnly());
        self::assertTrue($sut->getProperty('domainEmail')->isReadOnly());
        self::assertTrue($sut->getProperty('createdBy')->isReadOnly());
    }

    public function testPropertiesValuesAreNotChangeWhileConstructing(): void
    {
        $id = TenantId::fromString('f8d9310d-c441-45ab-b3b1-d38930080167');
        $name = TenantName::fromString('THe naME');
        $code = TenantCode::fromString('DeKo');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('b29ce81e-5dc7-420e-878d-1892f592a3a2'),
            UserEmail::fromString('user@tenant.com')
        );

        $result = (new NewTenant(
            $id,
            $name,
            $code,
            $domainEmail,
            $user,
        ))->toArray();

        self::assertEquals($id->toString(), $result['id']);
        self::assertEquals($name->toString(), $result['name']);
        self::assertEquals($code->toString(), $result['code']);
        self::assertEquals($domainEmail->toString(), $result['domainEmail']);
        self::assertEquals('b29ce81e-5dc7-420e-878d-1892f592a3a2', $result['createdBy']['id']);
    }

    public function testItCanBeConvertedToTenant(): void
    {
        $id = TenantId::fromString('f8d9310d-c441-45ab-b3b1-d38930080167');
        $name = TenantName::fromString('THe naME');
        $code = TenantCode::fromString('DeKo');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('b29ce81e-5dc7-420e-878d-1892f592a3a2'),
            UserEmail::fromString('user@tenant.com')
        );

        $result = (new NewTenant(
            $id,
            $name,
            $code,
            $domainEmail,
            $user,
        ))->toTenant();

        self::assertInstanceOf(Tenant::class, $result);

        $resultArray = $result->toArray();

        self::assertEquals('f8d9310d-c441-45ab-b3b1-d38930080167', $resultArray['id']);
        self::assertEquals('THe naME', $resultArray['name']);
        self::assertEquals('DeKo', $resultArray['code']);
        self::assertEquals('@tenant.com', $resultArray['domainEmail']);
        self::assertTrue($resultArray['isActive']);
        self::assertEquals(TenantStatus::WAITING_PROVISIONING, TenantStatus::from($resultArray['status']));
        self::assertEquals('b29ce81e-5dc7-420e-878d-1892f592a3a2', $resultArray['createdBy']['id']);
        self::assertEquals('user@tenant.com', $resultArray['createdBy']['email']);
    }

    public function testItCanBeConvertedToTenantCreatedEvent(): void
    {
        $id = TenantId::fromString('f8d9310d-c441-45ab-b3b1-d38930080167');
        $name = TenantName::fromString('THe naME');
        $code = TenantCode::fromString('DeKo');
        $domainEmail = TenantDomainEmail::fromString('@tenant.com');
        $user = new User(
            UserId::fromString('b29ce81e-5dc7-420e-878d-1892f592a3a2'),
            UserEmail::fromString('user@tenant.com')
        );

        $result = (new NewTenant(
            $id,
            $name,
            $code,
            $domainEmail,
            $user,
        ))->toTenantCreatedEvent();

        self::assertInstanceOf(TenantCreatedEvent::class, $result);
        self::assertEquals($code, $result->tenantCode);
    }
}
