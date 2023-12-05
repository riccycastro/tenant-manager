<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Infrastructure\Security;

use App\Containers\SecurityContainer\Application\FindsUserInterface;
use App\Containers\SecurityContainer\Domain\Model\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<User>
 */
final class FindCurrentUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private readonly FindsUserInterface $findsUser,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->findsUser->getCurrentUser($identifier);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // TODO: Implement upgradePassword() method.
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        assert($user instanceof User);

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
