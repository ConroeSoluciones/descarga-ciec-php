<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

enum Servicio: string
{
    case CSREPORTER = 'CSRN';

    case API_CIEC = 'CRAPI';
}
