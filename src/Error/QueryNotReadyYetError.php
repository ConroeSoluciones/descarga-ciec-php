<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Error;

use Csfacturacion\Descarga\Model\Uuid;
use Exception;

class QueryNotReadyYetError extends Exception
{
    public function __construct(Uuid $folio)
    {
        parent::__construct("La consulta con folio: $folio no ha finalizado");
    }
}
