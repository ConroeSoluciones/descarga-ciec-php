<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use Exception;
use InvalidArgumentException;
use JsonSerializable;

use function assert;
use function bin2hex;
use function chr;
use function ord;
use function preg_match;
use function random_bytes;
use function str_split;
use function strlen;
use function vsprintf;

class Uuid implements JsonSerializable
{
    final public function __construct(protected string $value)
    {
        $this->ensureIsValidUuid($value);
    }

    final public function value(): string
    {
        return $this->value;
    }

    final public function equals(self $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    private function ensureIsValidUuid(string $id): void
    {
        if (
            preg_match(
                '#^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$#',
                $id,
            ) === 0
        ) {
            throw new InvalidArgumentException("Invalid UUID format: $id");
        }
    }

    /**
     * @throws Exception
     */
    public static function random(): self
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = random_bytes(16);
        assert(strlen($data) === 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return new self(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
