<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

enum MediaType: string
{
    case JSON = 'application/json';

    case XML = 'application/xml';

    case ZIP = 'application/zip';
}
