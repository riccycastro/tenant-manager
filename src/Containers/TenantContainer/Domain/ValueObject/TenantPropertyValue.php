<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\Enum\PropertyType;
use App\Containers\TenantContainer\Domain\Exception\InvalidPropertyTypeException;
use Webmozart\Assert\Assert;

final class TenantPropertyValue
{
    /** @var string|int|float|bool|array|mixed[] */
    private readonly string|int|float|bool|array $value;
    private readonly PropertyType $type;

    /**
     * @param string|int|float|bool|array|mixed[] $value
     */
    private function __construct(PropertyType $type, string|int|float|bool|array $value)
    {
        if (PropertyType::BOOL === $type) {
            Assert::boolean($value);
        } elseif (PropertyType::ARRAY === $type) {
            Assert::isArray($value);
        } elseif (PropertyType::INT === $type) {
            Assert::integer($value);
        } elseif (PropertyType::FLOAT === $type) {
            Assert::float($value);
        } elseif (PropertyType::STRING === $type) {
            Assert::string($value);
        }

        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @throws InvalidPropertyTypeException
     */
    public static function fromValueType(PropertyType $type, string $value): self
    {
        if (PropertyType::BOOL === $type) {
            $value = '1' === $value || 'true' === strtolower((string) $value);
        } elseif (PropertyType::ARRAY === $type) {
            $value = json_decode($value);
        } elseif (PropertyType::INT === $type) {
            if (!is_numeric($value)) {
                throw new InvalidPropertyTypeException();
            }

            $value = (int) $value;
        } elseif (PropertyType::FLOAT === $type) {
            if (!is_numeric($value)) {
                throw new InvalidPropertyTypeException();
            }

            $value = (float) $value;
        }

        return new self($type, $value);
    }

    /**
     * @return string|int|float|bool|array|mixed[] $value
     */
    public function value(): array|bool|int|float|string
    {
        return $this->value;
    }

    public function getValueString(): string
    {
        if (PropertyType::ARRAY === $this->type) {
            return (string) json_encode($this->value);
        }

        return (string) $this->value; // @phpstan-ignore-line
    }

    public function type(): PropertyType
    {
        return $this->type;
    }
}
