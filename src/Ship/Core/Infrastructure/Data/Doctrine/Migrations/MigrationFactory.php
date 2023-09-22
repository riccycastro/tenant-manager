<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Data\Doctrine\Migrations;

use App\Ship\Core\Infrastructure\Data\Doctrine\Migrations\Interfaces\HashableMigrationInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory as MigrationFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final class MigrationFactory implements MigrationFactoryInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly LoggerInterface $logger,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory
    ) {
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $migration = new $migrationClassName(
            $this->connection,
            $this->logger
        );

        assert($migration instanceof AbstractMigration);

        if ($migration instanceof HashableMigrationInterface) {
            $migration->setPasswordHasherFactory($this->passwordHasherFactory);
        }

        return $migration;
    }
}
