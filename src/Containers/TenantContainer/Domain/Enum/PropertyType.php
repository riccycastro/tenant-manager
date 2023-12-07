<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Enum;

enum PropertyType: string
{
    case STRING = 'string';
    case BOOL = 'bool';
    case INT = 'int';
    case FLOAT = 'float';
    case ARRAY = 'array';
}
