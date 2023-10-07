<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Domain\Command;

use App\Containers\TenantContainer\Domain\Command\CreateTenantCommand;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Ship\Core\Domain\Command\CommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Domain\Command\CreateTenantCommand
 *
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 */
final class CreateTenantCommandTest extends TestCase
{
    public function testItIsCommand(): void
    {
        $sut = $this->buildSubjectUnderTest();

        self::assertInstanceOf(CommandInterface::class, $sut);
    }

    public function testPropertiesAreReadOnly(): void
    {
        $sut = new \ReflectionClass(CreateTenantCommand::class);

        self::assertTrue($sut->getProperty('id')->isReadOnly());
        self::assertTrue($sut->getProperty('name')->isReadOnly());
        self::assertTrue($sut->getProperty('code')->isReadOnly());
        self::assertTrue($sut->getProperty('domainEmail')->isReadOnly());
        self::assertTrue($sut->getProperty('user')->isReadOnly());
    }

    public function testItWontChangeTheValuesWhenConstructing(): void
    {
        $tenantId = TenantId::create();
        $tenantName = TenantName::fromString('aName');
        $tenantCode = TenantCode::fromString('a_code');
        $tenantDomainEmail = TenantDomainEmail::fromString('@tenant.com');
        $userId = UserId::create();
        $userEmail = UserEmail::fromString('user@tenant.com');

        $sut = new CreateTenantCommand(
            id: $tenantId,
            name: $tenantName,
            code: $tenantCode,
            domainEmail: $tenantDomainEmail,
            user: new User(
                $userId,
                $userEmail,
            )
        );

        self::assertEquals($tenantId, $sut->id);
        self::assertEquals($tenantName, $sut->name);
        self::assertEquals($tenantCode, $sut->code);
        self::assertEquals($tenantDomainEmail, $sut->domainEmail);
        self::assertEquals($userId, $sut->user->getId());
        self::assertEquals($userEmail, $sut->user->getEmail());
    }

    private function buildSubjectUnderTest(): CreateTenantCommand
    {
        return new CreateTenantCommand(
            id: TenantId::create(),
            name: TenantName::fromString('aName'),
            code: TenantCode::fromString('a_code'),
            domainEmail: TenantDomainEmail::fromString('@tenant.com'),
            user: new User(
                UserId::create(),
                UserEmail::fromString('user@tenant.com'),
            )
        );
    }
}
