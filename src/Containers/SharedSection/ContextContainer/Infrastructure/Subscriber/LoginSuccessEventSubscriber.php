<?php

declare(strict_types=1);

namespace App\Containers\SharedSection\ContextContainer\Infrastructure\Subscriber;

use App\Containers\SecurityContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Containers\SharedSection\ContextContainer\Application\Context;
use App\Containers\SharedSection\ContextContainer\Domain\Model\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

final class LoginSuccessEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage, private readonly Context $context)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [AuthenticationSuccessEvent::class => ['onLoginSuccessEvent', 7]];
    }

    public function onLoginSuccessEvent(AuthenticationSuccessEvent $event): void
    {
        $token = $this->tokenStorage->getToken();

        if ($token && $token->getUser() instanceof UserEntity) {
            $userEntity = $token->getUser();

            $this->context->setUser(new User(
                (int) $userEntity->getId(),
                $userEntity->getUserIdentifier(),
            ));
        }
    }
}
