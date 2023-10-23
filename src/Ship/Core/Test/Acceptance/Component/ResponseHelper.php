<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Acceptance\Component;

use Symfony\Component\HttpFoundation\Response;

final class ResponseHelper
{
    private ?Response $response;

    public function getResponse(): ?Response
    {
        if ($this->hasResponse()) {
            return $this->response;
        }
        throw new \RuntimeException('Trying to access response before initialization');
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function hasResponse(): bool
    {
        return isset($this->response);
    }
}
