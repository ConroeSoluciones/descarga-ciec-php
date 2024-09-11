<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Util;

use Csfacturacion\Descarga\Error\HttpComunicationError;
use Csfacturacion\Descarga\Model\HttpRequest;
use Csfacturacion\Descarga\Model\HttpResponse;
use Csfacturacion\Descarga\Model\MediaType;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient as BaseClient;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function fclose;
use function fopen;
use function fwrite;
use function is_null;
use function sprintf;

class HttpClient
{
    private HttpClientInterface $httpClient;

    public function __construct(?HttpClientInterface $httpClient = null)
    {
        if (is_null($httpClient)) {
            $httpClient = BaseClient::create();
        }

        $this->httpClient = $httpClient;
    }

    /**
     * @throws HttpComunicationError
     */
    public function send(HttpRequest $request): HttpResponse
    {
        $options = [
            'headers' => $request->getHeaders(),
        ];

        if ($request->getEntity() !== null) {
            $bodyType = $request->getHeader('Content-Type', MediaType::JSON->value)
            === MediaType::JSON->value
                ? 'json'
                : 'body';

            $options[$bodyType] = $request->getEntity();
        }

        try {
            $resp = $this->httpClient->request(
                $request->getMethod()->name,
                $request->getUri(),
                $options,
            );

            return new HttpResponse(
                $resp->getContent(false),
                $resp->getStatusCode(),
                $resp->getHeaders(false),
            );
        } catch (TransportExceptionInterface | HttpExceptionInterface $e) {
            throw new HttpComunicationError(message: $e->getMessage(), previous: $e);
        }
    }

    /**
     * @param callable(int, int, array{'start_time': int, 'redirect_count': int}): void | null $progressCallback
     */
    public function sendAndSave(HttpRequest $request, string $filepath, ?callable $progressCallback = null): void
    {
        try {
            $options = [
                'headers' => $request->getHeaders(),
                'on_progress' => $progressCallback,
            ];

            $response = $this->httpClient->request($request->getMethod()->name, $request->getUri(), $options);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                throw new RuntimeException(sprintf('Error al descargar el recurso. Código de estado: %d', $statusCode));
            }

            $fileHandle = fopen($filepath, 'wb');
            if ($fileHandle === false) {
                throw new RuntimeException(sprintf('No se pudo abrir el archivo en la ruta: %s', $filepath));
            }

            try {
                foreach ($this->httpClient->stream($response) as $chunk) {
                    fwrite($fileHandle, $chunk->getContent());
                }
            } finally {
                fclose($fileHandle);
            }
        } catch (TransportExceptionInterface $e) {
            throw new HttpComunicationError($e->getMessage());
        }
    }
}
