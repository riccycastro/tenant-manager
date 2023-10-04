<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Exception;

final class NoResultException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No result was found for query although at least one row was expected.');
    }
}
