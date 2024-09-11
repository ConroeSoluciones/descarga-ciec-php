<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model\Filter;

enum StatusFilter: string
{
    case VIGENTE = 'vigentes';

    case CANCELADO = 'cancelados';

    case TODOS = 'todos';
}
