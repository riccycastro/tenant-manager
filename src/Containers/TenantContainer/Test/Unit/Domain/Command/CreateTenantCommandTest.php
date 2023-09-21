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

final class CreateTenantCommandTest extends TestCase
{
    public function testItIsCommand(): void
    {
        $sut = $this->buildSubjectUnderTest();

        self::assertInstanceOf(CommandInterface::class, $sut);
    }

    public function testIdPropertyIsReadOnly(): void
    {
        $sut = new \ReflectionClass(CreateTenantCommand::class);

        self::assertTrue($sut->getProperty('id')->isReadOnly());
        self::assertTrue($sut->getProperty('name')->isReadOnly());
        self::assertTrue($sut->getProperty('code')->isReadOnly());
        self::assertTrue($sut->getProperty('domainEmail')->isReadOnly());
        self::assertTrue($sut->getProperty('user')->isReadOnly());
    }

    private function buildSubjectUnderTest(): CreateTenantCommand
    {
        return new CreateTenantCommand(
            id: TenantId::create(),
            name: TenantName::fromString('aName'),
            code: TenantCode::fromString('a_cde'),
            domainEmail: TenantDomainEmail::fromString('@tenant.com'),
            user: new User(
                UserId::create(),
                UserEmail::fromString('user@tenant.com')
            )
        );
    }
}
