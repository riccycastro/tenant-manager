<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\Data\Doctrine\Entity;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\TenantEntity;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Test\Unit\Infrastructure\Components\EntityInstantiator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\TenantEntity
 *
 * @uses \App\Containers\TenantContainer\Domain\Model\Tenant
 * @uses \App\Containers\TenantContainer\Domain\Model\User
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantId
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantName
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserEmail
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\UserId
 * @uses \App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\UserEntity
 */
final class TenantEntityTest extends TestCase
{
    use EntityInstantiator;

    private TenantEntity $sut;

    public function testItCanBeCreatedFromArray(): void
    {
        $result = TenantEntity::fromArray([
            'id' => '0ee50f0a-de10-49d6-a1fb-536916200620',
            'name' => 'PhakeOne',
            'code' => 'phakex',
            'domainEmail' => '@this.self',
            'createdBy' => $this->instantiateEntity(
                UserEntity::class,
                [
                    'id' => 'ce28e0a3-472c-4b0d-b32b-f7fa1a2582c3',
                    'email' => 'user@site.com',
                ],
            ),
        ]);

        self::assertInstanceOf(TenantEntity::class, $result);
    }

    public function testItCanBeConvertedToTenant(): void
    {
        $tenant = $this->sut->toTenant();

        self::assertInstanceOf(Tenant::class, $tenant);
        self::assertEquals([
            'id' => '11e01269-5bc2-45cc-b11d-5930f78c3edf',
            'name' => 'Fancy name',
            'code' => 'fancy_name',
            'domainEmail' => '@site.com',
            'createdBy' => [
                'id' => 'ce28e0a3-472c-4b0d-b32b-f7fa1a2582c3',
                'email' => 'user@site.com',
            ],
            'status' => TenantStatus::WAITING_PROVISIONING->value,
            'isActive' => true,
        ], $tenant->toArray());
    }

    public function testItCanBeUpdatedFromTenant(): void
    {
        $this->sut->update(new Tenant(
            TenantId::fromString('94079a0c-572b-489b-b75e-fe8f7ecff5a7'),
            TenantName::fromString('nameless'),
            TenantCode::fromString('a_kode'),
            TenantDomainEmail::fromString('@website.com'),
            new User(
                UserId::fromString('3b03c2e3-58e6-4eee-975a-3373cad8c152'),
                UserEmail::fromString('user@website.com')
            ),
            TenantStatus::READY,
            false,
        ));

        $tenant = $this->sut->toTenant();

        self::assertEquals([
            'id' => '11e01269-5bc2-45cc-b11d-5930f78c3edf',
            'name' => 'nameless',
            'code' => 'a_kode',
            'domainEmail' => '@website.com',
            'createdBy' => [
                'id' => 'ce28e0a3-472c-4b0d-b32b-f7fa1a2582c3',
                'email' => 'user@site.com',
            ],
            'status' => TenantStatus::READY->value,
            'isActive' => false,
        ], $tenant->toArray());
    }

    protected function setUp(): void
    {
        $this->sut = $this->instantiateEntity(
            TenantEntity::class,
            [
                'id' => '11e01269-5bc2-45cc-b11d-5930f78c3edf',
                'name' => 'Fancy name',
                'code' => 'fancy_name',
                'domainEmail' => '@site.com',
                'createdBy' => $this->instantiateEntity(
                    UserEntity::class,
                    [
                        'id' => 'ce28e0a3-472c-4b0d-b32b-f7fa1a2582c3',
                        'email' => 'user@site.com',
                    ],
                ),
                'status' => TenantStatus::WAITING_PROVISIONING->value,
                'isActive' => true,
            ],
        );
    }
}
