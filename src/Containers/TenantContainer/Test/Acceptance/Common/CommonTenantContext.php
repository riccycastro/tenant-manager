<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Acceptance\Common;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserEmail;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\Data\InMemory\Repository\TenantInMemoryRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;

final class CommonTenantContext implements Context
{
    public function __construct(
        private readonly TenantInMemoryRepository $tenants,
    ) {
    }

    /**
     * @Given /^I have a tenant with:$/
     */
    public function iHaveATenantWith(TableNode $table)
    {
        $data = [];

        foreach ($table->getColumnsHash() as $row) {
            $data[$row['field']] = $row['value'];
        }

        $tenant = new Tenant(
            id: isset($data['id']) ? TenantId::fromString($data['id']) : TenantId::create(),
            name: TenantName::fromString($data['name']),
            code: TenantCode::fromString($data['code']),
            domainEmail: TenantDomainEmail::fromString($data['domain email']),
            createdBy: new User(
                id: UserId::create(),
                email: UserEmail::fromString('user@site.com')
            ),
            status: TenantStatus::from($data['status']),
            isActive: 'true' === $data['is active'],
            properties: [],
        );

        $this->tenants->add($tenant);
    }
}
