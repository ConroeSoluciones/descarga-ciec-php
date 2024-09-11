<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Util;

use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\FoliosParams;
use Csfacturacion\Descarga\Model\HttpMethod;
use Csfacturacion\Descarga\Model\HttpRequest;
use Csfacturacion\Descarga\Model\MediaType;
use Csfacturacion\Descarga\Model\Parameters;
use Csfacturacion\Descarga\Model\Uuid;

class RequestFactory
{
    private const DEFAULT_URI = 'https://csfacturacion.com/webservices/csdescargasat/v3';

    private string $uri;

    public function __construct(string $uri = self::DEFAULT_URI)
    {
        $this->uri = $uri;
    }

    public function newQueryRequest(Credenciales $credencialesCs, Parameters $params): HttpRequest
    {
        $req = new HttpRequest(
            $this->uri . '/consultar',
            MediaType::JSON,
            HttpMethod::POST,
            $params,
        );

        $req->addHeader('rfc', $credencialesCs->getUser())
            ->addHeader('password', $credencialesCs->getPassword());

        return $req;
    }

    public function newFoliosQueryRequest(Credenciales $credencialesCs, FoliosParams $params): HttpRequest
    {
        $req = new HttpRequest(
            $this->uri . '/cfdiFolios',
            MediaType::JSON,
            HttpMethod::POST,
            $params,
        );

        $req->addHeader('rfc', $credencialesCs->getUser())
            ->addHeader('password', $credencialesCs->getPassword());

        return $req;
    }

    public function newProgressRequest(Uuid $queryId): HttpRequest
    {
        return new HttpRequest($this->uri . "/consultas/$queryId/progreso");
    }

    public function newSummaryRequest(Uuid $queryId): HttpRequest
    {
        return new HttpRequest($this->uri . "/consultas/$queryId/resumen");
    }

    public function newPageRequest(Uuid $queryId, int $page): HttpRequest
    {
        return new HttpRequest($this->uri . "/consultas/$queryId/$page");
    }

    public function newCfdiRequest(Uuid $uuid, MediaType $as): HttpRequest
    {
        return new HttpRequest($this->uri . "/cfdi/$uuid", $as);
    }

    public function newZipRequest(Uuid $queryId): HttpRequest
    {
        return new HttpRequest(
            $this->uri . "/consultas/$queryId",
            MediaType::ZIP,
        );
    }

    public function newRepeatRequest(Credenciales $credencialesCs, Uuid $queryId): HttpRequest
    {
        $req = new HttpRequest($this->uri . "/repetir?uuid=$queryId");
        $req->addHeader('rfc', $credencialesCs->getUser())
            ->addHeader('password', $credencialesCs->getPassword());

        return $req;
    }
}
