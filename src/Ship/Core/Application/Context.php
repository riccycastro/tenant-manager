<?php

declare(strict_types=1);

namespace App\Ship\Core\Application;

use App\Ship\Core\Domain\Model\User;

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
