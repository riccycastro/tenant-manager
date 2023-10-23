<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Acceptance\Component;

final class TokenStorage
{
    private string $token;

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        if (isset($this->token)) {
            return $this->token;
        }

        throw new \RuntimeException('Trying to access token before initialization.');
    }
}
