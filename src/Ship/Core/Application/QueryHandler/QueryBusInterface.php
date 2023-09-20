<?php

namespace App\Ship\Core\Application\QueryHandler;

use App\Ship\Core\Domain\Query\QueryInterface;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;
}
