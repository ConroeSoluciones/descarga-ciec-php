<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use Csfacturacion\Descarga\Util\Deserializable;
use InvalidArgumentException;

use function array_keys_exist;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class Progress implements Deserializable
{
    public function __construct(
        private readonly Status $status,
        private readonly int $found,
    ) {
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getFound(): int
    {
        return $this->found;
    }

    public static function fromJson(string $raw): self
    {
        /**
         * @var array{'estado': string, 'encontrados': int} $attrs
         */
        $attrs = (array) json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!array_keys_exist($attrs, ['estado', 'encontrados'])) {
            throw new InvalidArgumentException('Invalid attributes');
        }

        return new Progress(Status::from($attrs['estado']), $attrs['encontrados']);
    }
}
