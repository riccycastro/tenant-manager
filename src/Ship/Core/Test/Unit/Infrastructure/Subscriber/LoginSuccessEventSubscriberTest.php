<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\Subscriber;

use App\Containers\SecurityContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Application\Context;
use App\Ship\Core\Domain\Model\LoggedUser;
use App\Ship\Core\Infrastructure\Subscriber\LoginSuccessEventSubscriber;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @covers \App\Ship\Core\Infrastructure\Subscriber\LoginSuccessEventSubscriber
 *
 * @uses   \App\Ship\Core\Application\Context
 * @uses   \App\Ship\Core\Domain\Model\LoggedUser
 */
final class LoginSuccessEventSubscriberTest extends TestCase
{
    use ProphecyTrait;

    private LoginSuccessEventSubscriber $sut;

    private ObjectProphecy|TokenInterface $token;
    private ObjectProphecy|Security $tokenStorage;
    private Context $context;

    public function testItIsEventSubscriberInterface(): void
    {
        self::assertInstanceOf(EventSubscriberInterface::class, $this->sut);
    }

    public function testGetSubscriberEventsReturnsExpectedStructure(): void
    {
        $result = LoginSuccessEventSubscriber::getSubscribedEvents();

        self::assertEquals(
            [
                RequestEvent::class => ['onLoginSuccessEvent', 7],
            ],
            $result,
        );
    }

    public function testSetLoggedUserIsNotCalledIfTokenIsntInTokenStorage(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Logged user is not defined');

        $this->tokenStorage
            ->getToken()
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this->sut->onLoginSuccessEvent(
            $this->prophesize(RequestEvent::class)->reveal()
        );

        $this->context->getLoggedUser();
    }

    public function testSetLoggedUserIsNotCalledIfUserIsntInToken(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Logged user is not defined');

        $this->tokenStorage
            ->getToken()
            ->shouldBeCalled()
            ->willReturn($this->token->reveal())
        ;

        $this->token
            ->getUser()
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this->sut->onLoginSuccessEvent(
            $this->prophesize(RequestEvent::class)->reveal()
        );

        $this->context->getLoggedUser();
    }

    public function testSetLoggedUserIsNotCalledIfUserIsNotInstanceOfUserEntity(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Logged user is not defined');

        $this->tokenStorage
            ->getToken()
            ->shouldBeCalled()
            ->willReturn($this->token->reveal())
        ;

        $this->token
            ->getUser()
            ->shouldBeCalled()
            ->willReturn(new class() implements UserInterface {
                public function getRoles(): array
                {
                    return [];
                }

                public function eraseCredentials()
                {
                    // TODO: Implement eraseCredentials() method.
                }

                public function getUserIdentifier(): string
                {
                    return '';
                }
            })
        ;

        $this->sut->onLoginSuccessEvent(
            $this->prophesize(RequestEvent::class)->reveal()
        );

        $this->context->getLoggedUser();
    }

    public function testOnLoginSuccessEventSetsUser(): void
    {
        $userEntity = $this->prophesize(UserEntity::class);

        $userEntity
            ->getId()
            ->willReturn(1)
        ;

        $userEntity
            ->getUserIdentifier()
            ->willReturn('user@site.com')
        ;

        $this->tokenStorage
            ->getToken()
            ->shouldBeCalled()
            ->willReturn($this->token->reveal())
        ;

        $this->token
            ->getUser()
            ->shouldBeCalled()
            ->willReturn($userEntity)
        ;

        $this->sut->onLoginSuccessEvent(
            $this->prophesize(RequestEvent::class)->reveal()
        );

        $result = $this->context->getLoggedUser();

        self::assertInstanceOf(LoggedUser::class, $result);
    }

    protected function setUp(): void
    {
        $this->tokenStorage = $this->prophesize(Security::class);
        $this->context = new Context();
        $this->token = $this->prophesize(TokenInterface::class);

        $this->sut = new LoginSuccessEventSubscriber(
            $this->tokenStorage->reveal(),
            $this->context,
        );
    }
}
