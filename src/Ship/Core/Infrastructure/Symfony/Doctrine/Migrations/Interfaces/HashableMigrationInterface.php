<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Symfony\Doctrine\Migrations\Interfaces;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

interface HashableMigrationInterface
{
    public function setPasswordHasherFactory(PasswordHasherFactoryInterface $passwordHasherFactory): void;
}
