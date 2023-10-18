<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\Components;

use Doctrine\Instantiator\Instantiator;
use GeneratedHydrator\Configuration;

trait EntityInstantiator
{
    /**
     * @template T
     *
     * @param class-string<T>      $className
     * @param array<string, mixed> $data
     *
     * @return T
     */
    private function instantiateEntity(string $className, array $data): object
    {
        $instantiator = new Instantiator();

        $hydrator = new Configuration($className);

        return $hydrator
            ->createFactory()
            ->getHydrator()
            ->hydrate($data, $instantiator->instantiate($className))
        ;
    }
}
