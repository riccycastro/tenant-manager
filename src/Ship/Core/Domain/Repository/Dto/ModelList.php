<?php

declare(strict_types=1);

namespace App\Ship\Core\Domain\Repository\Dto;

/**
 * @template T of object
 */
final class ModelList
{
    /**
     * @param T[] $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $count,
    ) {
    }
}
