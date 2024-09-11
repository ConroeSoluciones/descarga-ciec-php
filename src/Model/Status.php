<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use function str_starts_with;

/**
 * los distintos STATUS posibles de una consulta.
 */
enum Status: string
{
    case EN_ESPERA = 'EN_ESPERA';
    case EN_PROCESO = 'EN_PROCESO';
    case DESCARGANDO = 'DESCARGANDO';
    case FALLO_AUTENTICACION = 'FALLO_AUTENTICACION';
    case FALLO_500_MISMO_HORARIO = 'FALLO_500_MISMO_HORARIO';
    case FALLO = 'FALLO';
    case COMPLETADO = 'COMPLETADO';
    case COMPLETADO_XML_FALTANTES = 'COMPLETADO_XML_FALTANTES';
    case REPETIR = 'REPETIR';

    public function isFinished(): bool
    {
        return str_starts_with($this->value, 'COMPLETADO') || $this->isFailed();
    }

    public function isFailed(): bool
    {
        return str_starts_with($this->value, 'FALLO');
    }

    public function isToRepeat(): bool
    {
        return $this === self::REPETIR;
    }
}
