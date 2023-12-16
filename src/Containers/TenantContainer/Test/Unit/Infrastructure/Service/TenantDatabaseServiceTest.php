<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\Service;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Infrastructure\Service\DatabaseServiceInterface;
use App\Containers\TenantContainer\Infrastructure\Service\TenantDatabaseService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\Service\TenantDatabaseService
 *
 * @uses   \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 */
final class TenantDatabaseServiceTest extends TestCase
{
    use ProphecyTrait;

    private TenantDatabaseService $sut;
    private ObjectProphecy|Connection $connectionMaster;
    private ObjectProphecy|Connection $connectionDefault;

    public function testItIsDatabaseServiceInterface(): void
    {
        self::assertInstanceOf(DatabaseServiceInterface::class, $this->sut);
    }

    public function testCreateTenantDatabaseUserExecutesTheExpectedQueries(): void
    {
        $tenantCode = TenantCode::fromString('a_c_ode');

        $this->connectionMaster
            ->executeQuery("CREATE USER `a_c_ode`@`%` IDENTIFIED BY 'strongPassword'")
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(Result::class)->reveal(),
            )
        ;

        $this->connectionMaster
            ->executeQuery('GRANT INSERT, UPDATE, SELECT, CREATE, REFERENCES, LOCK TABLES ON test_a_c_ode.* TO `a_c_ode`@`%`; FLUSH PRIVILEGES;')
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(Result::class)->reveal(),
            )
        ;

        $this->sut->createTenantDatabaseUser(
            $tenantCode,
            'strongPassword',
        );
    }

    public function testCreateDatabaseExecutesTheExpectedQuery(): void
    {
        $tenantCode = TenantCode::fromString('a_c_o_de');

        $this->connectionMaster
            ->executeQuery('CREATE DATABASE IF NOT EXISTS test_a_c_o_de CHARACTER SET utf8 COLLATE utf8_general_ci;')
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(Result::class)->reveal(),
            )
        ;

        $this->sut->createDatabase(
            $tenantCode,
        );
    }

    public function testDatabaseExistsExecutesTheExpectedQuery(): void
    {
        $tenantCode = TenantCode::fromString('a_c_o_d_e');

        $queryResult = $this->prophesize(Result::class);
        $queryResult->fetchAssociative()
            ->willReturn([1, 2, 3])
        ;

        $this->connectionMaster
            ->executeQuery("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'test_a_c_o_d_e';")
            ->shouldBeCalledOnce()
            ->willReturn($queryResult->reveal())
        ;

        $result = $this->sut->hasDatabase(
            $tenantCode,
        );

        self::assertTrue($result);
    }

    public function testDatabaseExistsReturnsFalseIfDatabaseDoesntExist(): void
    {
        $tenantCode = TenantCode::fromString('a_c_o_d_e');

        $queryResult = $this->prophesize(Result::class);
        $queryResult->fetchAssociative()
            ->willReturn([])
        ;

        $this->connectionMaster
            ->executeQuery(Argument::any())
            ->shouldBeCalledOnce()
            ->willReturn($queryResult->reveal())
        ;

        $result = $this->sut->hasDatabase(
            $tenantCode,
        );

        self::assertFalse($result);
    }

    public function testBeginDatabaseTransactionWorksAsExpected(): void
    {
        $this->connectionDefault
            ->beginTransaction()
            ->shouldBecalledOnce()
        ;

        $this->sut->beginDatabaseTransaction();
    }

    public function testCommitDatabaseTransactionExecutesCommitIfTransactionIsActive(): void
    {
        $this->connectionDefault
            ->isTransactionActive()
            ->shouldBecalledOnce()
            ->willReturn(true)
        ;

        $this->connectionDefault
            ->commit()
            ->shouldBeCalledOnce()
        ;

        $this->sut->commitDatabaseTransaction();
    }

    public function testCommitDatabaseTransactionDoesNotExecutesCommitIfTransactionIsntActive(): void
    {
        $this->connectionDefault
            ->isTransactionActive()
            ->shouldBecalledOnce()
            ->willReturn(false)
        ;

        $this->connectionDefault
            ->commit()
            ->shouldNotBeCalled()
        ;

        $this->sut->commitDatabaseTransaction();
    }

    protected function setUp(): void
    {
        $doctrine = $this->prophesize(ManagerRegistry::class);

        $this->connectionMaster = $this->prophesize(Connection::class);
        $this->connectionDefault = $this->prophesize(Connection::class);

        $doctrine
            ->getConnection('master')
            ->shouldBeCalledOnce()
            ->willReturn($this->connectionMaster->reveal())
        ;
        $doctrine
            ->getConnection('default')
            ->shouldBeCalledOnce()
            ->willReturn($this->connectionDefault->reveal())
        ;

        $_ENV['DATABASE_PREFIX'] = 'test_';

        $this->sut = new TenantDatabaseService(
            $doctrine->reveal(),
        );
    }
}
