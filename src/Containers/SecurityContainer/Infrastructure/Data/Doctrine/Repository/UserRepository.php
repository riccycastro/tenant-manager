<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Infrastructure\Data\Doctrine\Repository;

use App\Containers\SecurityContainer\Application\FindsUserInterface;
use App\Containers\SecurityContainer\Domain\Model\User;
use App\Containers\SecurityContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Infrastructure\Data\Doctrine\DoctrineRepository;
use App\Ship\Core\Infrastructure\Data\Doctrine\GetResultTrait;
use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 * @extends DoctrineRepository<UserEntity>
 */
final class UserRepository extends DoctrineRepository implements FindsUserInterface
{
    /**
     * @use GetResultTrait<User, UserEntity>
     */
    use GetResultTrait;

    private const ENTITY_CLASS = UserEntity::class;
    private const ALIAS = 'user';

    public function __construct(
        EntityManagerInterface $em,
    ) {
        parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCurrentUser(string $identifier): User
    {
        if ($user = $this->withEmail($identifier)->getResult()) {
            return $user;
        }

        throw new UserNotFoundException(sprintf('User with email %s not found.', $identifier));
    }

    public function withEmail(string $email): static
    {
        return $this->filter(static function (QueryBuilder $qb) use ($email): void {
            $qb->where(
                sprintf('%s.email = :email', self::ALIAS)
            )
                ->setParameter('email', $email)
            ;
        });
    }

    /**
     * @param UserEntity|null $entity
     */
    protected function entityToModel($entity): ?User
    {
        return $entity?->toUser();
    }
}
