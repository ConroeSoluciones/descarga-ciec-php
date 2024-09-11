<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

enum HttpMethod
{
    case GET;
    case POST;
    case PATCH;
    case PUT;
    case DELETE;
}
