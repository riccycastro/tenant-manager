<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230908162110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'First migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
        CREATE TABLE IF NOT EXISTS `user` (
            `id` varchar(36) not null primary key,
            `name` varchar(255) not null,
            `email` varchar(255) not null unique,
            `password` varchar(255) not null,
            `is_active` tinyint(1) not null default 1,
            `is_system` tinyint(1) not null default 0,
            `invited_by` varchar(36),
            `created_at` datetime default current_timestamp,
            `updated_at` datetime default current_timestamp on update current_timestamp,
            `deleted_at` datetime default null,
            foreign key (`invited_by`) references `user`(`id`)
        );'
        );

        $this->addSql(
            '
        CREATE TABLE IF NOT EXISTS `system_property` (
            `id` varchar(36) not null primary key,
            `key` varchar(255) not null,
            `value` varchar(255) not null,
            `created_by` varchar(36) not null,
            `created_at` datetime default current_timestamp,
            `updated_at` datetime default current_timestamp on update current_timestamp,
            `deleted_at` datetime default null,
            foreign key (`created_by`) references `user`(`id`)
        );'
        );

        $this->addSql(
            '
        CREATE TABLE IF NOT EXISTS `tenant` (
            `id` varchar(36) not null primary key,
            `name` varchar(255) not null,
            `code` varchar(255) not null unique,
            `status` ENUM(\'waiting_provisioning\', \'provisioning\', \'ready_for_migration\', \'ready\', \'deactivated\') DEFAULT \'waiting_provisioning\',
            `domain_email` VARCHAR(100) NOT NULL UNIQUE,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_by` varchar(36) not null,
            `created_at` datetime default current_timestamp,
            `updated_at` datetime default current_timestamp on update current_timestamp,
            `deleted_at` datetime default null,
            foreign key (`created_by`) references `user`(`id`)
        );'
        );

        $this->addSql(
            "
        CREATE TABLE IF NOT EXISTS `tenant_property` (
            `id` varchar(36) not null primary key,
            `tenant_id` varchar(36) not null,
            `name` varchar(255) not null,
            `value` varchar(255) not null,
            `type` enum('string', 'bool', 'int', 'float', 'array') default 'string',
            `created_by` varchar(36) not null,
            `created_at` datetime default current_timestamp,
            `updated_at` datetime default current_timestamp on update current_timestamp,
            `deleted_at` datetime default null,
            foreign key (`tenant_id`) references `tenant`(`id`),
            foreign key (`created_by`) references `user`(`id`)
        );"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `tenant_property`;');
        $this->addSql('DROP TABLE `tenant`;');
        $this->addSql('DROP TABLE `system_property`;');
        $this->addSql('DROP TABLE `user`;');
    }
}
