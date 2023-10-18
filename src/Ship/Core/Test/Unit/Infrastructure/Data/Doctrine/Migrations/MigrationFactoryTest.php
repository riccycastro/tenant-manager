<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\Data\Doctrine\Migrations;

use App\Ship\Core\Infrastructure\Data\Doctrine\Migrations\Interfaces\HashableMigrationInterface;
use App\Ship\Core\Infrastructure\Data\Doctrine\Migrations\MigrationFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory as MigrationFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @covers \App\Ship\Core\Infrastructure\Data\Doctrine\Migrations\MigrationFactory
 */
final class MigrationFactoryTest extends TestCase
{
    use ProphecyTrait;

    private MigrationFactory $sut;

    private ObjectProphecy|Connection $connection;
    private ObjectProphecy|LoggerInterface $logger;
    private ObjectProphecy|PasswordHasherFactoryInterface $passwordHasherFactory;

    public function testItIsMigrationFactoryInterface(): void
    {
        self::assertInstanceOf(MigrationFactoryInterface::class, $this->sut);
    }

    public function testCreateVersionCallsSetPasswordIfInstanceOfHashableMigrationInterface(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('setPasswordHasherFactory called');

        $this->connection
            ->createSchemaManager()
            ->willReturn(
                $this->prophesize(AbstractSchemaManager::class)->reveal()
            )
        ;

        $this->connection
            ->getDatabasePlatform()
            ->willReturn(
                $this->prophesize(AbstractPlatform::class)->reveal()
            )
        ;

        $class = new class($this->connection->reveal(), $this->logger->reveal()) extends AbstractMigration implements HashableMigrationInterface {
            public function setPasswordHasherFactory(PasswordHasherFactoryInterface $passwordHasherFactory): void
            {
                throw new \RuntimeException('setPasswordHasherFactory called');
            }

            public function up(Schema $schema): void
            {
            }
        };

        $this->sut->createVersion(get_class($class));
    }

    public function testCreateVersionCallsDoesntSetPasswordIfNotInstanceOfHashableMigrationInterface(): void
    {
        $this->connection
            ->createSchemaManager()
            ->willReturn(
                $this->prophesize(AbstractSchemaManager::class)->reveal()
            )
        ;

        $this->connection
            ->getDatabasePlatform()
            ->willReturn(
                $this->prophesize(AbstractPlatform::class)->reveal()
            )
        ;

        $class = new class($this->connection->reveal(), $this->logger->reveal()) extends AbstractMigration {
            public function setPasswordHasherFactory(PasswordHasherFactoryInterface $passwordHasherFactory): void
            {
                throw new \RuntimeException('setPasswordHasherFactory called');
            }

            public function up(Schema $schema): void
            {
            }
        };

        $result = $this->sut->createVersion(get_class($class));

        self::assertInstanceOf(AbstractMigration::class, $result);
    }

    protected function setUp(): void
    {
        $this->connection = $this->prophesize(Connection::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->passwordHasherFactory = $this->prophesize(PasswordHasherFactoryInterface::class);

        $this->sut = new MigrationFactory(
            $this->connection->reveal(),
            $this->logger->reveal(),
            $this->passwordHasherFactory->reveal(),
        );
    }
}
