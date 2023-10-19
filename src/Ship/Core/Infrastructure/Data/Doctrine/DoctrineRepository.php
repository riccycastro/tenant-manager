<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Data\Doctrine;

use App\Ship\Core\Domain\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Webmozart\Assert\Assert;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class DoctrineRepository implements RepositoryInterface
{
    protected QueryBuilder $queryBuilder;
    private ?int $page = null;
    private ?int $itemsPerPage = null;

    public function __construct(
        protected EntityManagerInterface $em,
        string $entityClass,
        string $alias,
    ) {
        $this->queryBuilder = $this->em->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias)
        ;
    }

    public function withPagination(int $page, int $itemsPerPage): static
    {
        Assert::positiveInteger($page);
        Assert::positiveInteger($itemsPerPage);

        $firstResult = ($this->page - 1) * $this->itemsPerPage;
        $maxResults = $this->itemsPerPage;

        return $this->filter(static function (QueryBuilder $qb) use ($firstResult, $maxResults) {
            $qb->setFirstResult($firstResult)->setMaxResults($maxResults);
        });
    }

    /**
     * @param callable(QueryBuilder): void $filter
     */
    protected function filter(callable $filter): static
    {
        $cloned = clone $this;
        $filter($cloned->queryBuilder);

        return $cloned;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function count(): int
    {
        return (int) (clone $this->queryBuilder)
            ->select('count(1)')
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult()
        ;
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
