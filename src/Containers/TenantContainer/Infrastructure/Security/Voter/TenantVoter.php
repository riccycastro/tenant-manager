<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Security\Voter;

use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\PatchTenantInputDto;
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
        return $this->supportsAttribute($attribute)
            && $this->supportsType((string) get_class($subject));
    }

    public function supportsAttribute(string $attribute): bool
    {
        return in_array($attribute, [self::TENANT_CREATE, self::TENANT_UPDATE]);
    }

    public function supportsType(string $subjectType): bool
    {
        return in_array($subjectType, [TenantResource::class, PatchTenantInputDto::class]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::TENANT_UPDATE, self::TENANT_CREATE => true,
            default => false,
        };
    }
}
