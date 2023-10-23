<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Acceptance\Framework;

use App\Ship\Core\Test\Acceptance\Component\RequestHelper;
use App\Ship\Core\Test\Acceptance\Component\ResponseHelper;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

final class ListTenantContext implements Context
{
    public function __construct(
        private readonly RequestHelper $requestHelper,
        private readonly ResponseHelper $responseHelper,
    ) {
    }

    /**
     * @When /^I list the tenants$/
     */
    public function iListTheTenants()
    {
        $this->responseHelper->setResponse(
            $this->requestHelper->makeGetRequest(
                urlName: '_api_/tenants{._format}_get_collection',
                queryParams: ['page' => 1],
            )
        );
    }

    /**
     * @Then /^I should see the following entries on the tenant list:$/
     */
    public function iShouldSeeTheFollowingEntriesOnTheTenantList(TableNode $tableNode)
    {
        Assert::assertTrue($this->responseHelper->hasResponse());

        $response = $this->responseHelper->getResponse();

        Assert::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        Assert::assertIsArray($responseData);

        foreach ($tableNode->getColumnsHash() as $row) {
            Assert::assertArrayHasKey('code', $row);
            $tenantCode = $row['code'];

            $tenantArray = array_filter($responseData, function (array $tenant) use ($tenantCode) {
                return $tenant['code'] === $tenantCode;
            });

            Assert::assertNotEmpty($tenantArray);
            Assert::assertCount(1, $tenantArray);

            $tenantArray = [...$tenantArray][0];

            foreach ($row as $property => $value) {
                Assert::assertArrayHasKey($property, $tenantArray);

                if (is_bool($tenantArray[$property])) {
                    Assert::assertEquals('true' === $value, $tenantArray[$property]);
                    continue;
                }
                Assert::assertEquals($value, (string) $tenantArray[$property]);
            }
        }
    }
}
