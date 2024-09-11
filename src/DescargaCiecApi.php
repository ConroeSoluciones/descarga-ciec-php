<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga;

use Csfacturacion\Descarga\Error\InvalidQueryError;
use Csfacturacion\Descarga\Model\FoliosParams;
use Csfacturacion\Descarga\Model\Parameters;
use Csfacturacion\Descarga\Model\Uuid;

/**
 * Esta es la interfaz principal para iniciar la comunicación con el WS.
 * A partir de aquí, se pueden realizar nuevas consultas, búsquedas de folios
 * previamente consultados y repetir consultas pasadas.
 */
interface DescargaCiecApi
{
    /**
     * Realiza una consulta para obtener los CFDIs que correspondan de acuerdo
     * a los parámetros especificados.
     *
     * @param Parameters $params los parámetros de búsqueda.
     *
     * @return QueryRetrieverApi la consulta que contiene la
     * funcionalidad para obtener el status y resultados de la misma.
     *
     * @throws InvalidQueryError si ocurre un problema
     * con los parámetros de la consulta.
     */
    public function makeQuery(Parameters $params): QueryRetrieverApi;

    /**
     * Es posible buscar consultas por folio específico, en caso que se hayan
     * realizado previamente y se quiera consultar sus resultados.
     *
     * @param Uuid $folio de la consulta previamente realizada.
     *
     * @return QueryRetrieverApi la consulta con el folio
     * especificado.
     *
     * @throws InvalidQueryError si no se encuentra
     * ninguna consulta con el folio especificado.
     */
    public function search(Uuid $folio): QueryRetrieverApi;

    /**
     * Si la consulta con el folio dado está en status REPETIR, este método
     * repetirá la consulta para obtener los resultados necesarios.
     *
     * @param Uuid $folio de la consulta previamente realizada.
     *
     * @return QueryRetrieverApi la consulta que se repetirá.
     *
     * @throws InvalidQueryError si no es posible
     * repetir la consulta (e.g. status != "REPETIR" o no existe el folio).
     */
    public function repeat(Uuid $folio): QueryRetrieverApi;

    /**
     * Realiza una consulta por folios (UUID)
     *
     * @param FoliosParams $params Los UUID a buscar
     *
     * @return QueryRetrieverApi la consulta para monitorear y obtener resultados
     *
     * @throws InvalidQueryError si ocurre algun error con los parametros
     */
    public function byFolios(FoliosParams $params): QueryRetrieverApi;
}
