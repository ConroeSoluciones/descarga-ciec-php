<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

enum CfdiStatus: string
{
    case VIGENTE = 'VIGENTE';

    case CANCELADO = 'CANCELADO';
}
