<?php

declare(strict_types=1);

namespace App\Containers\SharedSection\ContextContainer\Application;

use App\Containers\SharedSection\ContextContainer\Domain\Model\User;

final class Context
{
    private User $user;

    public function setUser(User $user): void
    {
        if (!isset($this->user)) {
            $this->user = $user;
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function getUserId(): int
    {
        if (isset($this->user)) {
            return $this->user->getId();
        }

        throw new \RuntimeException('Logged user is not defined');
    }
}
