<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model\Filter;

enum CaseFilter: string
{
    case EMITIDAS = 'emitidas';

    case RECIBIDAS = 'recibidas';

    case TODAS = 'todas';
}
