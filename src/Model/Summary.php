<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use Csfacturacion\Descarga\Util\Deserializable;
use InvalidArgumentException;

use function array_keys_exist;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class Summary implements Deserializable
{
    public function __construct(
        private readonly int $total,
        private readonly int $pages,
        private readonly bool $hasMissingXml,
        private readonly int $cancelados,
    ) {
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function hasMissingXml(): bool
    {
        return $this->hasMissingXml;
    }

    public function getCancelados(): int
    {
        return $this->cancelados;
    }

    public static function fromJson(string $raw): Summary
    {
        /**
         * @var array{
         *     'total': int,
         *     'paginas': int,
         *     'fechasMismoHorario': string[],
         *     'xmlFaltantes': bool,
         *     'cancelados': int
         * } $attrs
         */
        $attrs = (array) json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $mustExist = ['total', 'paginas', 'xmlFaltantes', 'cancelados', 'fechasMismoHorario'];
        if (!array_keys_exist($attrs, $mustExist)) {
            throw new InvalidArgumentException('Invalid attributes');
        }

        return new Summary(
            $attrs['total'],
            $attrs['paginas'],
            $attrs['xmlFaltantes'],
            $attrs['cancelados'],
        );
    }
}
