<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Infrastructure\UI\Queue\MessageHandler;

use App\Containers\SecurityContainer\Application\FindsUserInterface;
use App\Ship\Core\Application\Context;
use App\Ship\Core\Domain\Model\LoggedUser;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

final class MessengerBusMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly FindsUserInterface $findsUser,
        private readonly Context $context,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->last(ReceivedStamp::class)) {
            $systemUser = $this->findsUser->getSystemUser();

            $this->context->setLoggedUser(new LoggedUser(
                $systemUser->getId(),
                $systemUser->getUserIdentifier(),
            ));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
