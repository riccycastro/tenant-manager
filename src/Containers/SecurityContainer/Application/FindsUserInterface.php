<?php

namespace App\Containers\SecurityContainer\Application;

use App\Containers\SecurityContainer\Domain\Model\User;

interface FindsUserInterface
{
    public function getCurrentUser(string $identifier): User;

    public function getSystemUser(): User;
}
