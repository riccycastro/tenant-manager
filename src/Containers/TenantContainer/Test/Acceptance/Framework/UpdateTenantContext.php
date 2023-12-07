<?php

namespace App\Containers\TenantContainer\Test\Acceptance\Framework;

use App\Ship\Core\Test\Acceptance\Component\RequestHelper;
use App\Ship\Core\Test\Acceptance\Component\ResponseHelper;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

class UpdateTenantContext implements Context
{
    public function __construct(
        private readonly RequestHelper $requestHelper,
        private readonly ResponseHelper $responseHelper,
    ) {
    }

    /**
     * @When I update the tenant :tenantCode with:
     */
    public function iUpdateTheTenantWith(string $tenantCode, TableNode $table)
    {
        $data = [];

        foreach ($table->getColumnsHash() as $row) {
            $data[$row['field']] = $row['value'];
        }

        $this->responseHelper->setResponse(
            $this->requestHelper->makePatchRequest(
                urlName: 'tenant_update_patch',
                urlParams: [
                    'code' => $tenantCode,
                ],
                bodyParams: $data,
            )
        );
    }

    /**
     * @Then I should see that the returned structure fields have expected values:
     */
    public function iShouldSeeThatTheReturnedStructureFieldsHaveExpectedValues(TableNode $table)
    {
        Assert::assertTrue($this->responseHelper->hasResponse());
        Assert::assertEquals(Response::HTTP_OK, $this->responseHelper->getResponse()->getStatusCode());

        $data = [];

        foreach ($table->getColumnsHash() as $row) {
            $data[$row['field']] = $row['value'];
        }

        $response = json_decode($this->responseHelper->getResponse()->getContent(), true);

        foreach ($data as $key => $value) {
            Assert::assertEquals($value, $response[$key]);
        }
    }
}
