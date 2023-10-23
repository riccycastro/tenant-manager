<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Test\Acceptance\Common;

use App\Containers\SecurityContainer\Application\PersistsUserInterface;
use App\Containers\SecurityContainer\Domain\Model\User;
use App\Containers\SecurityContainer\Test\Acceptance\Component\TokenFactory;
use App\Ship\Core\Test\Acceptance\Component\TokenStorage;
use Behat\Behat\Context\Context;

final class CommonSecurityContext implements Context
{
    public function __construct(
        private readonly PersistsUserInterface $persistsUser,
        private readonly TokenFactory $tokenFactory,
        private readonly TokenStorage $tokenStorage,
    ) {
    }

    /**
     * @Given /^I am authenticated as system user$/
     */
    public function iAmAuthenticatedAsSystemUser(): void
    {
        $user = new User(
            'c5cc9fc2-9d2c-41c6-9ad8-fdb2dfd24038',
            'system@system.com',
            'strongOne'
        );

        $this->persistsUser->saveAsNew($user);

        $this->tokenStorage
            ->setToken(
                $this->tokenFactory->create($user)
            )
        ;
    }
}
