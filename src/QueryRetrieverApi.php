<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga;

use Csfacturacion\Descarga\Error\CfdiNotFoundError;
use Csfacturacion\Descarga\Error\NotEnoughResultsError;
use Csfacturacion\Descarga\Error\QueryNotReadyYetError;
use Csfacturacion\Descarga\Error\XmlNotFoundError;
use Csfacturacion\Descarga\Error\ZipError;
use Csfacturacion\Descarga\Model\CfdiMeta;
use Csfacturacion\Descarga\Model\Progress;
use Csfacturacion\Descarga\Model\Summary;
use Csfacturacion\Descarga\Model\Uuid;

interface QueryRetrieverApi
{
    /**
     * El progreso acutal de la consulta indica:
     * El estatus
     * CFDI encontrados hasta el momento
     *
     * @see Progress
     *
     * @return Progress El progreso actual de la consulta, reportado por el WS.
     */
    public function getProgress(): Progress;

    /**
     * Cuando una consulta ha terminado, su status puede ser:
     * FALLO_AUTENTICACION
     * FALLO_500_MISMO_HORARIO
     * FALLO
     * COMPLETADO
     * COMPLETADO_XML_FALTANTES
     *
     * Para verificar que no se haya completado con error, verificar el método
     * self::isFailed() o directamente el status de la consulta.
     *
     * @return bool true si se devuelve cualquiera de los status anteriores
     * o false de otro modo.
     */
    public function isFinished(): bool;

    /**
     * Cualquiera de los siguientes status deben marcar esta consulta como
     * fallo:
     *
     * FALLO_AUTENTICACION
     * FALLO_500_MISMO_HORARIO
     * FALLO
     *
     * @return bool true si se devuelve cualquiera de los status anteriores
     * o false de otro modo.
     */
    public function isFailed(): bool;

    /**
     * Si la consulta ha sido marcada con status REPETIR, no habrá ningún
     * resultado disponible y será necesario repetir esta consulta.
     *
     * @see repetir()
     *
     * @return bool true si el status es REPETIR, false de otro modo.
     */
    public function isToRepeat(): bool;

    /**
     * Una consulta puede no generar resultados, si eso sucede este método
     * devuelve true.
     *
     * @return bool true si no generó resultados la consulta, false de otro
     * modo.
     *
     * @throws QueryNotReadyYetError si la consulta no ha finalizado aun.
     */
    public function hasResults(): bool;

    /**
     * Cuando se realiza una consulta a través de un IDescargaSAT, se genera
     * un folio único que identifica la consulta.
     *
     * @return Uuid el UUID que identifica a la consulta.
     */
    public function getFolio(): Uuid;

    /**
     * Obtiene el resumen de una consulta ya terminada:
     *
     * @throws QueryNotReadyYetError si la consulta no ha finalizado aun.
     */
    public function getSummary(): Summary;

    /**
     * Los resultados se devuelven paginados, por lo que este método permite
     * obtener un arreglo de CFDIs (simples) para una página determinada.
     *
     * @param int $page Que se desea obtener.
     *
     * @return CfdiMeta[] El total de registros encontrados en la página dada
     *
     * @throws NotEnoughResultsError Si el no. de pag. no existe
     * @throws QueryNotReadyYetError si la consulta no ha finalizado aun.
     */
    public function getResults(int $page): array;

    /**
     * Permite obtener un CFDI específico, resultante de esta consulta,
     * como cadena.
     *
     * @param Uuid $folio El folio (UUID) del CFDI.
     *
     * @return CfdiMeta Metadata del CFDI en un arreglo asociativo
     *
     * @throws CfdiNotFoundError
     */
    public function getCfdi(Uuid $folio): CfdiMeta;

    /**
     * Devuelve el XML del CFDIMeta dado. En ocasiones puede no haber un XML
     * asociado, en estos casos devuelve null.
     *
     * @param Uuid $folio del CFDIMeta.
     *
     * @return string el XML asociado con el CFDIMeta
     *
     * @throws XmlNotFoundError
     */
    public function getXml(Uuid $folio): string;

    /**
     * Obtiene los resultados de la consulta comprimidos en un ZIP
     *
     * @param callable(int, int, array{'start_time': int, 'redirect_count': int}): void | null $downloadCallback
     *
     * @throws ZipError si no se puede generar el ZIP
     */
    public function asZip(string $filepath, ?callable $downloadCallback = null): void;
}
