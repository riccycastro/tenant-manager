<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\ApiPlatform\OpenApi;

use ApiPlatform\Api\FilterInterface;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\OpenApi\TenantCodeFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\ApiPlatform\OpenApi\TenantCodeFilter
 */
final class TenantCodeFilterTest extends TestCase
{
    private TenantCodeFilter $sut;

    public function testItIsFilterInterface(): void
    {
        self::assertInstanceOf(FilterInterface::class, $this->sut);
    }

    public function testGetDescription(): void
    {
        $result = $this->sut->getDescription('');

        self::assertEquals(
            [
                'code' => [
                    'property' => 'code',
                    'type' => 'string',
                    'required' => false,
                ],
            ],
            $result,
        );
    }

    protected function setUp(): void
    {
        $this->sut = new TenantCodeFilter();
    }
}
