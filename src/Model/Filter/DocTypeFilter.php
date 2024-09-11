<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model\Filter;

enum DocTypeFilter: string
{
    case CFDI = 'cfdi';

    case RETENCION = 'retencion';
}
