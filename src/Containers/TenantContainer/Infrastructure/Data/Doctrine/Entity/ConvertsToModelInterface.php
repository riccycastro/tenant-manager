<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity;

/**
 * @template T of object
 */
interface ConvertsToModelInterface
{
    /**
     * @return T
     */
    public function toModel();
}
