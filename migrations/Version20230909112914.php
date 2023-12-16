<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Containers\SecurityContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Infrastructure\Data\Doctrine\Migrations\Interfaces\HashableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230909112914 extends AbstractMigration implements HashableMigrationInterface
{
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    public function getDescription(): string
    {
        return 'Add system user';
    }

    public function up(Schema $schema): void
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(new UserEntity());
        $hashedPassword = $passwordHasher->hash($_ENV['SYSTEM_PASSWORD']);
        $this->addSql(
            sprintf(
                "INSERT INTO `user` (`id`, `name`, `email`, `password`, `is_system`) VALUES('%s', 'System', '%s', '%s', 1);",
                Uuid::uuid4()->toString(),
                $_ENV['SYSTEM_EMAIL'],
                $hashedPassword,
            )
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            sprintf(
                "DELETE FROM `user` WHERE `email` = '%s';",
                $_ENV['SYSTEM_EMAIL'],
            )
        );
    }

    public function setPasswordHasherFactory(PasswordHasherFactoryInterface $passwordHasherFactory): void
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
    }
}
