<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Application;

use App\Ship\Core\Application\Context;
use App\Ship\Core\Application\FindsLoggedUserInterface;
use App\Ship\Core\Domain\Model\LoggedUser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Ship\Core\Application\Context
 *
 * @uses   \App\Ship\Core\Domain\Model\LoggedUser
 */
final class ContextTest extends TestCase
{
    public function testItIsFindsLoggedUserInterface(): void
    {
        $sut = new Context();

        self::assertInstanceOf(FindsLoggedUserInterface::class, $sut);
    }

    public function testLoggedUserCanBeSetAndGet(): void
    {
        $loggedUser = new LoggedUser(
            'dad01c19-d954-4cd7-8e73-603a59a57a85',
            'user@site.com'
        );

        $sut = new Context();

        $sut->setLoggedUser($loggedUser);

        $returnedLoggedUser = $sut->getLoggedUser();

        self::assertEquals($loggedUser, $returnedLoggedUser);
    }

    public function testLoggedUserIsNotSetTwice(): void
    {
        $loggedUser = new LoggedUser(
            'dad01c19-d954-4cd7-8e73-603a59a57a85',
            'user@site.com'
        );
        $loggedUser2 = new LoggedUser(
            '0e4847b9-e492-45c9-a1da-cd16832ebab6',
            'user.b@siter.com'
        );

        $sut = new Context();

        $sut->setLoggedUser($loggedUser);
        $sut->setLoggedUser($loggedUser2);

        $returnedLoggedUser = $sut->getLoggedUser();
        self::assertEquals($loggedUser, $returnedLoggedUser);
    }

    public function testItThrowsExceptionWhileTryingToGetUnsetLoggedUser(): void
    {
        $this->expectException(\RuntimeException::class);

        $sut = new Context();
        $sut->getLoggedUser();
    }
}
