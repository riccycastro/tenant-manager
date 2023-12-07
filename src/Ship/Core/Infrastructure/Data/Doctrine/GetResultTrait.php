<?php

namespace App\Ship\Core\Infrastructure\Data\Doctrine;

use App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity\ConvertsToModelInterface;
use App\Ship\Core\Domain\Repository\Dto\ModelList;
use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;
use App\Ship\Core\Infrastructure\Exception\NoResultException;

/**
 * @template T of object
 * @template S of mixed
 */
trait GetResultTrait
{
    /**
     * @return ModelList<T>
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getListResult(): ModelList
    {
        $items = $this->getResults();

        try {
            $count = $this->count();
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            throw new NonUniqueResultException();
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw new NoResultException();
        }

        return new ModelList(
            items: $items,
            count: $count,
        );
    }

    /**
     * @return T[]
     */
    public function getResults(): array
    {
        $entities = $this->getEntitiesResult();

        return array_map([$this, 'entityToModel'], $entities);
    }

    /**
     * @return S[]
     */
    private function getEntitiesResult(): array
    {
        return $this->queryBuilder->getQuery()->getResult();
    }

    /**
     * @return T|null
     *
     * @throws NonUniqueResultException
     */
    public function getResult()
    {
        return $this->entityToModel($this->getEntityResult());
    }

    /**
     * @param S|null $entity
     *
     * @return T|null
     */
    abstract protected function entityToModel(?ConvertsToModelInterface $entity);

    /**
     * @return S|null
     *
     * @throws NonUniqueResultException
     */
    protected function getEntityResult()
    {
        try {
            return $this->queryBuilder->getQuery()->getOneOrNullResult();
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            throw new NonUniqueResultException();
        }
    }
}
