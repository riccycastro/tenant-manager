<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Repository;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Exception\UserNotFoundException;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\TenantEntity;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Infrastructure\Data\Doctrine\DoctrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @extends DoctrineRepository<TenantEntity>
 */
final class TenantRepository extends DoctrineRepository implements PersistsTenantInterface, FindsTenantInterface
{
    private const ENTITY_CLASS = TenantEntity::class;
    private const ALIAS = 'tenant';

    public function __construct(
        EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
    }

    public function saveAsNew(Tenant $tenant): Tenant
    {
        $data = $this->preparePersistenceDataFromTenant($tenant);

        $entity = TenantEntity::fromArray($data);

        $this->em->persist($entity);
        $this->em->flush();

        $this->eventDispatcher->dispatch($tenant->toTenantCreatedEvent());

        return $tenant;
    }

    /**
     * @return  array{
     *          id: string,
     *          name: string,
     *          code: string,
     *          domainEmail: string,
     *          createdBy: UserEntity,
     *          }
     */
    private function preparePersistenceDataFromTenant(Tenant $tenant): array
    {
        $data = [];

        $data['id'] = $tenant->getId()->toString();
        $data['name'] = $tenant->getName()->toString();
        $data['code'] = $tenant->getCode()->toString();
        $data['domainEmail'] = $tenant->getDomainEmail()->toString();
        $data['createdBy'] = $this->findUserEntity($tenant->getCreatedByIdentifier());

        return $data;
    }

    /**
     * @throws UserNotFoundException
     */
    private function findUserEntity(UserId $userId): UserEntity
    {
        if ($userEntity = $this->getRepository(UserEntity::class)->find($userId->toString())) {
            return $userEntity;
        }

        throw UserNotFoundException::fromUserId($userId);
    }

    public function withCode(TenantCode $code): static
    {
        return $this->filter(static function (QueryBuilder $qb) use ($code): void {
            $qb->where(
                sprintf('%s.code = :code', self::ALIAS)
            )
                ->setParameter('code', $code->toString());
        });
    }
}
