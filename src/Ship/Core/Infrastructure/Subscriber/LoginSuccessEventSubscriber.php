<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Subscriber;

use App\Containers\SecurityContainer\Infrastructure\Data\Doctrine\Entity\UserEntity;
use App\Ship\Core\Application\Context;
use App\Ship\Core\Domain\Model\LoggedUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class LoginSuccessEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $tokenStorage,
        private readonly Context $context,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => ['onLoginSuccessEvent', 7]];
    }

    public function onLoginSuccessEvent(RequestEvent $event): void
    {
        $token = $this->tokenStorage->getToken();

        if ($token && $token->getUser() instanceof UserEntity) {
            $userEntity = $token->getUser();

            $this->context->setLoggedUser(new LoggedUser(
                (string) $userEntity->getId(),
                $userEntity->getUserIdentifier(),
            ));
        }
    }
}
