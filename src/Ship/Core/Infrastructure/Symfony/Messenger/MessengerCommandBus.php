<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Symfony\Messenger;

use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Domain\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerCommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    /**
     * @throws \Throwable
     */
    public function dispatch(CommandInterface $command): mixed
    {
        try {
            return $this->handle($command);
        } catch (HandlerFailedException $e) {
            /** @var array{0: \Throwable} $exceptions */
            $exceptions = $e->getNestedExceptions();

            throw $exceptions[0];
        }
    }
}
