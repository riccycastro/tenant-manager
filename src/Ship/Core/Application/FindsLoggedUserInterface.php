<?php

namespace App\Ship\Core\Application;

use App\Ship\Core\Domain\Model\LoggedUser;

interface FindsLoggedUserInterface
{
    public function getLoggedUser(): LoggedUser;
}
