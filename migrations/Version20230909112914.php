<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Containers\SecurityContainer\Entity\User;
use App\Ship\Core\Doctrine\Migrations\Interfaces\HashableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
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
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(new User());
        $hashedPassword = $passwordHasher->hash($_ENV['SYSTEM_PASSWORD']);
        $this->addSql(
            "INSERT INTO `user` (`name`, `email`, `password`) VALUES('System', 'system@smart-community.com', '$hashedPassword');"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `user` WHERE `email` = 'system@system.com';");
    }

    public function setPasswordHasherFactory(PasswordHasherFactoryInterface $passwordHasherFactory): void
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
    }
}
