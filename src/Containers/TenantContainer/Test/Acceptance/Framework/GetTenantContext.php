<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Acceptance\Framework;

use App\Ship\Core\Test\Acceptance\Component\RequestHelper;
use App\Ship\Core\Test\Acceptance\Component\ResponseHelper;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

final class GetTenantContext implements Context
{
    public function __construct(
        private readonly RequestHelper $requestHelper,
        private readonly ResponseHelper $responseHelper,
    ) {
    }

    /**
     * @When /^get the tenant with code "([^"]*)"$/
     */
    public function getTheTenantWithCode(string $tenantCode)
    {
        $this->responseHelper->setResponse(
            $this->requestHelper->makeGetRequest(
                urlName: '_api_/tenants/{code}_get',
                urlParams: ['code' => $tenantCode],
            )
        );
    }

    /**
     * @Then /^I should see the following tenant:$/
     */
    public function iShouldSeeTheFollowingTenant(TableNode $tableNode)
    {
        Assert::assertTrue($this->responseHelper->hasResponse());

        $response = $this->responseHelper->getResponse();
        Assert::assertEquals(200, $response->getStatusCode());

        $tenant = json_decode($response->getContent(), true);

        foreach ($tableNode->getColumnsHash() as $row) {
            foreach ($row as $property => $value) {
                Assert::assertArrayHasKey($property, $tenant);

                if (is_bool($tenant[$property])) {
                    Assert::assertEquals('true' === $value, $tenant[$property]);
                    continue;
                }

                Assert::assertEquals(
                    $value,
                    $tenant[$property],
                );
            }
        }
    }
}
