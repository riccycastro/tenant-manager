<?php

namespace App\Ship\Core\Application\CommandHandler;

use App\Ship\Core\Domain\Command\CommandInterface;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): mixed;
}
