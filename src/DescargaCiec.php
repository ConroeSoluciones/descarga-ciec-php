<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga;

use Csfacturacion\Descarga\Error\HttpComunicationError;
use Csfacturacion\Descarga\Error\InvalidQueryError;
use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\FoliosParams;
use Csfacturacion\Descarga\Model\HttpRequest;
use Csfacturacion\Descarga\Model\Parameters;
use Csfacturacion\Descarga\Model\Uuid;
use Csfacturacion\Descarga\Util\HttpClient;
use Csfacturacion\Descarga\Util\RequestFactory;
use RuntimeException;

use function is_string;
use function sprintf;

class DescargaCiec implements DescargaCiecApi
{
    private Credenciales $credenciales;

    private RequestFactory $requestFactory;

    private HttpClient $apiClient;

    public function __construct(Credenciales $credenciales)
    {
        $this->credenciales = $credenciales;
        $this->requestFactory = new RequestFactory();
        $this->apiClient = new HttpClient();
    }

    public function makeQuery(Parameters $params): QueryRetrieverApi
    {
        $request = $this->requestFactory->newQueryRequest($this->credenciales, $params);

        return $this->handleQueryRequest($request);
    }

    public function search(Uuid $folio): QueryRetrieverApi
    {
        $this->validateQuery($folio);

        $query = $this->newQuery($folio);

        // si tiene status REPETIR, iníciala de nuevo
        if ($query->isToRepeat()) {
            $query = $this->repeat($folio);
        }

        return $query;
    }

    public function repeat(Uuid $folio): QueryRetrieverApi
    {
        $this->validateQuery($folio);
        $this->apiClient->send($this->requestFactory->newRepeatRequest($this->credenciales, $folio));

        return $this->newQuery($folio);
    }

    public function byFolios(FoliosParams $params): QueryRetrieverApi
    {
        $request = $this->requestFactory->newFoliosQueryRequest($this->credenciales, $params);

        return $this->handleQueryRequest($request);
    }

    /**
     * @throws InvalidQueryError
     */
    private function validateQuery(Uuid $folio): void
    {
        $response = $this->apiClient->send($this->requestFactory->newSummaryRequest($folio));

        if ($response->isServerError()) {
            throw new HttpComunicationError('Error con el servicio de descarga. Intentelo más tarde');
        }

        if ($response->isClientError()) {
            throw new InvalidQueryError("La consulta con folio $folio no existe");
        }
    }

    private function newQuery(Uuid $folio): QueryRetriever
    {
        return new QueryRetriever($folio, $this->apiClient, $this->requestFactory);
    }

    /**
     * @throws InvalidQueryError
     */
    private function handleQueryRequest(HttpRequest $request): QueryRetrieverApi
    {
        $response = $this->apiClient->send($request);

        if ($response->isServerError()) {
            throw new HttpComunicationError(
                'Ocurrio un error con el servicio de descarga. Intentelo más tarde',
            );
        }

        /**
         * @var array{
         *     'data': array{
         *         'uuid': string
         *     }
         * } | array{
         *     'error': string | array{
         *         'message': string,
         *         'code': int
         *     }
         * } $payload
         */
        $payload = $response->bodyAsArray();

        // el gateway proceso la info (no hay esquema de autenticacion, credenciales invalidas, etc) - 401
        // datos invalidos
        if ($response->isClientError()) {
            throw new InvalidQueryError(
                sprintf(
                    'Ocurrio un error al realizar la consulta. %s. Codigo HTTP : %s',
                    $payload['error']['message'] ?? 'error desconocido',
                    $response->getCode(),
                ),
            );
        }

        // TODOU OK pero hay un problema con el contrato
        if (isset($payload['error']) && is_string($payload['error'])) {
            throw new InvalidQueryError(
                sprintf(
                    'Ocurrio un error al realizar la consulta.  %s. Codigo HTTP : %s',
                    $payload['error'],
                    $response->getCode(),
                ),
            );
        }
        if (!isset($payload['data'])) {
            throw new RuntimeException('Unexpected response format');
        }

        return $this->newQuery(new Uuid($payload['data']['uuid']));
    }
}
