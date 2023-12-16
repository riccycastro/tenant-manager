<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Command;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\Exception\TenantNotFoundException;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(name: 'tenant:requeue', description: 'Requeue a tenant to create the database')]
final class RequeueTenantCreatedEventCommand extends Command
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly FindsTenantInterface $findsTenant,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument('tenant_code', InputArgument::REQUIRED, 'The tenant code that will be requeued');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenantCode = TenantCode::fromString($input->getArgument('tenant_code'));
        $tenant = $this->findsTenant
            ->withCode($tenantCode)
            ->getResult()
        ;

        if (null === $tenant) {
            throw TenantNotFoundException::fromTenantCode($tenantCode);
        }

        if (!$tenant->hasStatus(TenantStatus::WAITING_PROVISIONING)) {
            throw new \Exception('Tenant expected to be on status: '.TenantStatus::WAITING_PROVISIONING->value);
        }

        $this->eventDispatcher->dispatch(
            new TenantCreatedEvent(
                $tenantCode
            )
        );

        return Command::SUCCESS;
    }
}
