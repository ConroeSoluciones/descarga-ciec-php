<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Util;

use JsonException;

interface Deserializable
{
    /**
     * @return self|self[]
     *
     * @throws JsonException
     */
    public static function fromJson(string $raw): self | array;
}
