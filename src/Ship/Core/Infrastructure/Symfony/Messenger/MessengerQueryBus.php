<?php

declare(strict_types=1);

namespace App\Ship\Core\Infrastructure\Symfony\Messenger;

use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use App\Ship\Core\Domain\Query\QueryInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerQueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    /**
     * @throws \Throwable
     */
    public function ask(QueryInterface $query): mixed
    {
        try {
            return $this->handle($query);
        } catch (HandlerFailedException $e) {
            /** @var array{0: \Throwable} $exceptions */
            $exceptions = $e->getNestedExceptions();

            throw $exceptions[0];
        }
    }
}
