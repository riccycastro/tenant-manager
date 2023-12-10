<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Repository;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Application\UpdatesTenantInterface;
use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\Exception\UserNotFoundException;
use App\Containers\TenantContainer\Domain\Model\NewTenant;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\TenantProperty;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\ConvertsToModelInterface;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\TenantEntity;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\TenantPropertyEntity;
use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Infrastructure\Data\Doctrine\DoctrineRepository;
use App\Ship\Core\Infrastructure\Data\Doctrine\GetResultTrait;
use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @extends DoctrineRepository<TenantEntity>
 */
final class TenantRepository extends DoctrineRepository implements PersistsTenantInterface, UpdatesTenantInterface, FindsTenantInterface
{
    /**
     * @use GetResultTrait<Tenant, TenantEntity>
     */
    use GetResultTrait;

    private const ENTITY_CLASS = TenantEntity::class;
    private const ALIAS = 'tenant';

    public function __construct(
        EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
    }

    public function saveAsNew(NewTenant $newTenant): Tenant
    {
        $data = $this->preparePersistenceDataFromTenant($newTenant);

        $entity = TenantEntity::fromArray($data);

        $this->em->persist($entity);
        $this->em->flush();

        $this->eventDispatcher->dispatch($newTenant->toTenantCreatedEvent());

        return $entity->toModel();
    }

    /**
     * @return array<string, mixed>
     */
    private function preparePersistenceDataFromTenant(NewTenant $newTenant): array
    {
        $data = $newTenant->toArray();
        $data['createdBy'] = $this->findUserEntity(UserId::fromString($data['createdBy']['id']));

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

    /**
     * @throws NonUniqueResultException
     */
    public function save(Tenant $tenant): Tenant
    {
        $tenantEntity = $this->withCode($tenant->getCode())->getEntityResult();

        if (null === $tenantEntity) {
            throw TenantNotFoundException::fromTenantCode($tenant->getCode());
        }

        $tenantEntity->update($tenant);
        $tenantEntity = $this->processTenantProperties(
            $tenantEntity,
            $tenant,
        );

        $this->em->flush();

        return $tenantEntity->toModel();
    }

    public function withCode(TenantCode $code): static
    {
        return $this->filter(static function (QueryBuilder $qb) use ($code): void {
            $qb->where(
                sprintf('%s.code = :code', self::ALIAS)
            )
                ->setParameter('code', $code->toString())
            ;
        });
    }

    /**
     * @param ConvertsToModelInterface<Tenant>|null $entity
     */
    protected function entityToModel(?ConvertsToModelInterface $entity): ?Tenant
    {
        return $entity?->toModel();
    }

    private function processTenantProperties(TenantEntity $tenantEntity, Tenant $tenant): TenantEntity
    {
        $tenantPropertyEntityCollection = $tenantEntity->getTenantProperties();

        /** @var ArrayCollection<int, TenantPropertyEntity> $toBeSavedTenantProperties */
        $toBeSavedTenantProperties = new ArrayCollection(array_map(function (TenantProperty $tenantProperty) use ($tenantPropertyEntityCollection, $tenantEntity) {
            if ($tenantPropertyEntity = $tenantPropertyEntityCollection->get($tenantProperty->getName()->toString())) {
                $tenantPropertyEntity->setValue($tenantProperty->getStringValue());

                return $tenantPropertyEntity;
            }

            return new TenantPropertyEntity(
                id: $tenantProperty->getId()->toString(),
                name: $tenantProperty->getName()->toString(),
                value: $tenantProperty->getStringValue(),
                type: $tenantProperty->getType(),
                createdBy: $this->findUserEntity($tenantProperty->getCreatedBy()->getId()),
                tenant: $tenantEntity,
            );
        }, $tenant->getProperties()));

        $tenantEntity->addProperties($toBeSavedTenantProperties);

        return $tenantEntity;
    }
}
