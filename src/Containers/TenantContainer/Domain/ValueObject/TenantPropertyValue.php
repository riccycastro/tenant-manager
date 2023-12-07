<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\ValueObject;

use App\Containers\TenantContainer\Domain\Enum\PropertyType;
use Webmozart\Assert\Assert;

final class TenantPropertyValue
{
    /** @var string|int|bool|array|mixed[] */
    private readonly string|int|bool|array $value;
    private readonly PropertyType $type;

    /**
     * @param string|int|bool|array|mixed[] $value
     */
    private function __construct(PropertyType $type, string|int|bool|array $value)
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
     * @param string|int|bool|array|mixed[] $value
     */
    public static function fromValueType(PropertyType $type, string|int|bool|array $value): self
    {
        return new self($type, $value);
    }

    /**
     * @return string|int|bool|array|mixed[] $value
     */
    public function value(): array|bool|int|string
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
