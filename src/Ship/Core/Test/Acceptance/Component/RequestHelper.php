<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Acceptance\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RequestHelper
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TokenStorage $tokenStorage,
        private readonly KernelInterface $kernel,
    ) {
    }

    /**
     * @param array<string, mixed> $urlParams
     * @param array<string, mixed> $bodyParams
     *
     * @throws \Exception
     */
    public function makePostRequest(string $urlName, array $urlParams = [], array $bodyParams = []): Response
    {
        $uri = $this->urlGenerator->generate($urlName, $urlParams);

        $content = json_encode($bodyParams);

        if (false === $content) {
            throw new \RuntimeException('Invalid body params'.json_last_error_msg());
        }

        $request = Request::create(
            uri: $uri,
            method: Request::METHOD_POST,
            content: $content
        );

        $request->headers->add(
            $this->getHeaderData()
        );

        return $this->kernel->handle($request);
    }

    /**
     * @param array<string, mixed> $urlParams
     * @param array<string, mixed> $bodyParams
     *
     * @throws \Exception
     */
    public function makePatchRequest(string $urlName, array $urlParams = [], array $bodyParams = []): Response
    {
        $uri = $this->urlGenerator->generate($urlName, $urlParams);

        $content = json_encode($bodyParams);

        if (false === $content) {
            throw new \RuntimeException('Invalid body params'.json_last_error_msg());
        }

        $request = Request::create(
            uri: $uri,
            method: Request::METHOD_PATCH,
            content: $content
        );

        $request->headers->add(
            $this->getHeaderData()
        );

        return $this->kernel->handle($request);
    }

    /**
     * @param array<string, mixed> $urlParams
     * @param array<string, mixed> $queryParams
     *
     * @throws \Exception
     */
    public function makeGetRequest(string $urlName, array $urlParams = [], array $queryParams = []): Response
    {
        $uri = $this->urlGenerator->generate($urlName, $urlParams);

        $request = Request::create(
            uri: $uri,
            method: Request::METHOD_GET,
            parameters: $queryParams,
        );

        $request->headers->add(
            $this->getHeaderData()
        );

        return $this->kernel->handle($request);
    }

    /**
     * @return string[]
     */
    private function getHeaderData(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->tokenStorage->getToken(),
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];
    }
}
