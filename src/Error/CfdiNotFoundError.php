<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Error;

use Csfacturacion\Descarga\Model\Uuid;
use Exception;

class CfdiNotFoundError extends Exception
{
    public function __construct(Uuid $uuid)
    {
        parent::__construct("El CFDI con el $uuid no existe");
    }
}
