<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Exception;

final class NonUniqueResultException extends \Exception
{
    public function __construct()
    {
        parent::__construct('More than one result was found for query although one row or none was expected.');
    }
}
