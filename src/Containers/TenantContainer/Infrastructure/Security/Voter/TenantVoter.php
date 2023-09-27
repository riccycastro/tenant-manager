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
    public const TENANT_UPDATE = 'tenant.update';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::TENANT_CREATE, self::TENANT_UPDATE])
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
            case self::TENANT_UPDATE:
                return true;
        }

        return false;
    }
}
