<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Infrastructure\Data\InMemory\Repository;

use App\Containers\SecurityContainer\Application\FindsUserInterface;
use App\Containers\SecurityContainer\Application\PersistsUserInterface;
use App\Containers\SecurityContainer\Domain\Model\User;
use App\Ship\Core\Infrastructure\Data\InMemory\InMemoryRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 * @extends InMemoryRepository<User>
 */
final class UserInMemoryRepository extends InMemoryRepository implements FindsUserInterface, PersistsUserInterface
{
    public function getCurrentUser(string $identifier): User
    {
        if (isset($this->entities[$identifier])) {
            return $this->entities[$identifier];
        }

        throw new UserNotFoundException(sprintf('User with email %s not found.', $identifier));
    }

    public function saveAsNew(User $user): User
    {
        $this->entities[$user->getUserIdentifier()] = $user;

        return $user;
    }

    public function getSystemUser(): User
    {
        foreach ($this->entities as $entity) {
            return $entity;
        }

        throw new UserNotFoundException('System user not found');
    }
}
