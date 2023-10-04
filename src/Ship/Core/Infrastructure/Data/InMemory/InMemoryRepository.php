<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Data\InMemory;

use App\Ship\Core\Domain\Repository\RepositoryInterface;
use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class InMemoryRepository implements RepositoryInterface
{
    /**
     * @var T[]|array<string, T>
     */
    protected array $entities = [];

    /**
     * @return T|null
     *
     * @throws NonUniqueResultException
     */
    public function getResult()
    {
        if (empty($this->entities)) {
            return null;
        }

        if (1 === count($this->entities)) {
            return reset($this->entities);
        }

        throw new NonUniqueResultException();
    }

    /**
     * @return T[]
     */
    public function getResults(): array
    {
        // todo@rcastro - implement this
        return [];
    }

    public function withPagination(int $page, int $itemsPerPage): static
    {
        // todo@rcastro - implement this
        return $this;
    }

    /**
     * @param callable(mixed, mixed=): bool $filter
     */
    protected function filter(callable $filter): static
    {
        $cloned = clone $this;
        $cloned->entities = array_filter($cloned->entities, $filter);

        return $cloned;
    }
}
