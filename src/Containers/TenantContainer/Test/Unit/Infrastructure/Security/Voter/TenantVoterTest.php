<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\Security\Voter;

use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use App\Containers\TenantContainer\Infrastructure\Security\Voter\TenantVoter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\Security\Voter\TenantVoter
 *
 * @uses   \App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource
 */
final class TenantVoterTest extends TestCase
{
    use ProphecyTrait;

    public function testTenantCreateIsSupported(): void
    {
        $sut = new TenantVoter();

        self::assertTrue($sut->supportsAttribute('tenant.create'));
    }

    public function testTenantUpdateIsSupported(): void
    {
        $sut = new TenantVoter();

        self::assertTrue($sut->supportsAttribute('tenant.update'));
    }

    public function testItSupportTypeTenantResource(): void
    {
        $sut = new TenantVoter();

        self::assertTrue(
            $sut->supportsType(
                get_class(new TenantResource())
            ),
        );
    }

    public function testItDenyAccessIfUserNotInstanceOfUserInterface(): void
    {
        /** @var ObjectProphecy<TokenInterface> $token */
        $token = $this->prophesize(TokenInterface::class);

        $token
            ->getUser()
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $sut = new TenantVoter();

        $result = $sut->vote(
            $token->reveal(),
            new TenantResource(),
            [TenantVoter::TENANT_CREATE],
        );

        self::assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoteReturnsGrandAccessIfTenantCreate(): void
    {
        /** @var ObjectProphecy<TokenInterface> $token */
        $token = $this->prophesize(TokenInterface::class);

        $token
            ->getUser()
            ->shouldBeCalled()
            ->willReturn(new class() implements UserInterface {
                public function getRoles(): array
                {
                    return [];
                }

                public function eraseCredentials()
                {
                }

                public function getUserIdentifier(): string
                {
                    return '';
                }
            })
        ;

        $sut = new TenantVoter();

        $result = $sut->vote(
            $token->reveal(),
            new TenantResource(),
            [TenantVoter::TENANT_CREATE],
        );

        self::assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVoteReturnsGrandAccessIfTenantUpdate(): void
    {
        /** @var ObjectProphecy<TokenInterface> $token */
        $token = $this->prophesize(TokenInterface::class);

        $token
            ->getUser()
            ->shouldBeCalled()
            ->willReturn(new class() implements UserInterface {
                public function getRoles(): array
                {
                    return [];
                }

                public function eraseCredentials()
                {
                }

                public function getUserIdentifier(): string
                {
                    return '';
                }
            })
        ;

        $sut = new TenantVoter();

        $result = $sut->vote(
            $token->reveal(),
            new TenantResource(),
            [TenantVoter::TENANT_UPDATE],
        );

        self::assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
}
