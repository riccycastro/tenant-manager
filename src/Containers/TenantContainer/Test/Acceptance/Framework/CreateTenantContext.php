<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Acceptance\Framework;

use App\Ship\Core\Test\Acceptance\Component\RequestHelper;
use App\Ship\Core\Test\Acceptance\Component\ResponseHelper;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

final class CreateTenantContext implements Context
{
    public function __construct(
        private readonly RequestHelper $requestHelper,
        private readonly ResponseHelper $responseHelper,
    ) {
    }

    /**
     * @When /^I create a new tenant with:$/
     */
    public function iCreateANewTenantWith(TableNode $table): void
    {
        $data = [];

        foreach ($table->getColumnsHash() as $row) {
            $data[$row['field']] = $row['value'];
        }

        $this->responseHelper->setResponse(
            $this->requestHelper->makePostRequest(
                urlName: 'tenant_create',
                bodyParams: [
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'domainEmail' => $data['domain email'],
                ],
            )
        );
    }

    /**
     * @Then /^I should see that it was created successfully$/
     */
    public function iShouldSeeThatItWasCreatedSuccessfully(): void
    {
        Assert::assertTrue($this->responseHelper->hasResponse());
        Assert::assertEquals(Response::HTTP_CREATED, $this->responseHelper->getResponse()->getStatusCode());
    }
}
