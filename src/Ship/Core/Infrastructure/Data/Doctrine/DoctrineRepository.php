<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Data\Doctrine;

use App\Ship\Core\Domain\Repository\RepositoryInterface;
use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class DoctrineRepository implements RepositoryInterface
{
    private QueryBuilder $queryBuilder;

    public function __construct(
        protected EntityManagerInterface $em,
        string $entityClass,
        string $alias,
    ) {
        $this->queryBuilder = $this->em->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias);
    }

    /**
     * @return T|null
     *
     * @throws NonUniqueResultException
     */
    public function getResult()
    {
        $cloned = clone $this;

        try {
            return $cloned->queryBuilder->getQuery()->getOneOrNullResult();
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            throw new NonUniqueResultException();
        }
    }

    /**
     * @return T[]
     */
    public function getResults(): array
    {
        return [];
    }

    protected function filter(callable $filter): static
    {
        $cloned = clone $this;
        $filter($cloned->queryBuilder);

        return $cloned;
    }

    /**
     * @template S of object
     *
     * @param class-string<S> $entityName
     *
     * @return ObjectRepository<S>
     */
    protected function getRepository(string $entityName): ObjectRepository
    {
        return $this->em->getRepository($entityName);
    }

    protected function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }
}
