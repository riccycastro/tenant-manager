<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Security\Voter;

use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, TenantResource>
 */
final class TenantVoter extends Voter
{
    public const TENANT_CREATE = 'tenant.create';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::TENANT_CREATE])
            && $subject instanceof TenantResource;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        assert($subject instanceof TenantResource);

        switch ($attribute) {
            case self::TENANT_CREATE:
                return true;
        }

        return false;
    }
}
