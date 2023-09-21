<?php

declare(strict_types=1);

namespace App\Ship\Core\Application;

use App\Ship\Core\Domain\Model\LoggedUser;

final class Context implements FindsLoggedUserInterface
{
    private LoggedUser $loggedUser;

    public function setLoggedUser(LoggedUser $loggedUser): void
    {
        if (!isset($this->loggedUser)) {
            $this->loggedUser = $loggedUser;
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function getLoggedUser(): LoggedUser
    {
        if (isset($this->loggedUser)) {
            return $this->loggedUser;
        }

        throw new \RuntimeException('Logged user is not defined');
    }
}
