<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use function is_string;

class Contribuyente
{
    private readonly Rfc $rfc;
    private readonly string $razonSocial;

    public function __construct(Rfc | string $rfc, string $razonSocial)
    {
        if (is_string($rfc)) {
            $this->rfc = new Rfc($rfc);
        } else {
            $this->rfc = $rfc;
        }

        $this->razonSocial = $razonSocial;
    }

    public function getRfc(): Rfc
    {
        return $this->rfc;
    }

    public function getRazonSocial(): string
    {
        return $this->razonSocial;
    }
}
