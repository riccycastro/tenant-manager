<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\Exception\InvalidTenantPropertyIdValueException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\UuidInterface;

final class TenantPropertyId
{
    private readonly UuidInterface $id;

    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @throws InvalidTenantPropertyIdValueException
     */
    public static function fromString(string $id): self
    {
        try {
            return new self(Uuidv4::fromString($id));
        } catch (InvalidUuidStringException $exception) {
            throw InvalidTenantPropertyIdValueException::fromValue($id);
        }
    }

    public static function create(): self
    {
        return new self(UuidV4::uuid4());
    }

    public function toString(): string
    {
        return $this->id->toString();
    }
}
