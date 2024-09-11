<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Error;

use Csfacturacion\Descarga\Model\Uuid;
use Exception;

class XmlNotFoundError extends Exception
{
    public function __construct(Uuid $folio)
    {
        parent::__construct("El XML del CFDI con folio: $folio no existe");
    }
}
