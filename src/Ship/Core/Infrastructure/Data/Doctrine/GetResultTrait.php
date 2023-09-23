<?php

namespace App\Ship\Core\Infrastructure\Data\Doctrine;

use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;

/**
 * @template T of object
 * @template S of mixed
 */
trait GetResultTrait
{
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

    /**
     * @param S|null $entity
     *
     * @return T|null
     */
    abstract protected function entityToModel($entity);

    /**
     * @return T[]
     */
    public function getResults(): array
    {
        return [];
    }
}
