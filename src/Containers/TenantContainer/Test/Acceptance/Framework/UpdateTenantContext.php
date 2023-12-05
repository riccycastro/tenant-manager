<?php

namespace App\Containers\TenantContainer\Test\Acceptance\Framework;

use App\Ship\Core\Test\Acceptance\Component\RequestHelper;
use App\Ship\Core\Test\Acceptance\Component\ResponseHelper;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;

class UpdateTenantContext implements Context
{
    public function __construct(
        private readonly RequestHelper $requestHelper,
        private readonly ResponseHelper $responseHelper,
    ) {
    }

    /**
     * @When /^I update the tenant "my\-homespot" with:$/
     */
    public function iUpdateTheTenantWith($arg1, TableNode $table)
    {
        $data = [];

        foreach ($table->getColumnsHash() as $row) {
            $data[$row['field']] = $row['value'];
        }
    }
}
