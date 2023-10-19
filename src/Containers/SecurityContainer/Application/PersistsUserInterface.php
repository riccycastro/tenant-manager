<?php

namespace App\Containers\SecurityContainer\Application;

use App\Containers\SecurityContainer\Domain\Model\User;

interface PersistsUserInterface
{
    public function saveAsNew(User $user): User;
}
