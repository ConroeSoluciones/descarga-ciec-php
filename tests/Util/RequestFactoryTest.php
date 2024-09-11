<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Util;

use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\Filter\CaseFilter;
use Csfacturacion\Descarga\Model\Filter\StatusFilter;
use Csfacturacion\Descarga\Model\HttpMethod;
use Csfacturacion\Descarga\Model\MediaType;
use Csfacturacion\Descarga\Model\ParametersBuilder;
use Csfacturacion\Descarga\Model\Servicio;
use Csfacturacion\Descarga\Model\Uuid;
use Csfacturacion\Descarga\Util\RequestFactory;
use Csfacturacion\Test\Descarga\TestCase;
use DateInterval;
use DateTimeImmutable;

class RequestFactoryTest extends TestCase
{
    private RequestFactory $requestFactory;

    public const DEFAULT_URI = 'https://csfacturacion.com/webservices/csdescargasat/v3';

    private Uuid $queryId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestFactory = new RequestFactory();
        $this->queryId = Uuid::random();
    }

    public function testNewProgressRequest(): void
    {
        $req = $this->requestFactory->newProgressRequest($this->queryId);
        $this->assertEquals(
            self::DEFAULT_URI . "/consultas/$this->queryId/progreso",
            $req->getUri(),
        );
        $this->assertEquals(HttpMethod::GET, $req->getMethod());
    }

    public function testNewSummaryRequest(): void
    {
        $req = $this->requestFactory->newSummaryRequest($this->queryId);

        $this->assertEquals(
            self::DEFAULT_URI . "/consultas/$this->queryId/resumen",
            $req->getUri(),
        );
        $this->assertEquals(HttpMethod::GET, $req->getMethod());
    }

    public function testNewPageRequest(): void
    {
        $page = 1;
        $req = $this->requestFactory->newPageRequest($this->queryId, $page);

        $this->assertEquals(
            self::DEFAULT_URI . "/consultas/$this->queryId/$page",
            $req->getUri(),
        );
        $this->assertEquals(HttpMethod::GET, $req->getMethod());
    }

    public function testNewZipRequest(): void
    {
        $req = $this->requestFactory->newZipRequest($this->queryId);

        $this->assertEquals(
            self::DEFAULT_URI . "/consultas/$this->queryId",
            $req->getUri(),
        );
        $this->assertEquals(HttpMethod::GET, $req->getMethod());
        $this->assertEquals(MediaType::ZIP->value, $req->getHeader('Accept'));
    }

    public function testNewCfdiRequest(): void
    {
        $cfdiFolio = Uuid::random();
        $req = $this->requestFactory->newCfdiRequest($cfdiFolio, MediaType::JSON);
        $this->assertEquals(
            self::DEFAULT_URI . "/cfdi/$cfdiFolio",
            $req->getUri(),
        );
        $this->assertEquals(HttpMethod::GET, $req->getMethod());
        $this->assertEquals(MediaType::JSON->value, $req->getHeader('Accept'));
    }

    public function testNewQueryRequest(): void
    {
        $end = new DateTimeImmutable('now');
        $credenciales = new Credenciales('AAA010101AAA', 'PASS');
        $params = (new ParametersBuilder())
            ->accesoSat(new Credenciales('AAA010101AAA', 'CIEC'))
            ->fechaInicio($end->sub(DateInterval::createFromDateString('1 year')))
            ->fechaFin($end)
            ->status(StatusFilter::TODOS)
            ->caso(CaseFilter::TODAS)
            ->servicio(Servicio::API_CIEC)
            ->build();

        $req = $this->requestFactory->newQueryRequest($credenciales, $params);
        $this->assertEquals(
            self::DEFAULT_URI . '/consultar',
            $req->getUri(),
        );
        $this->assertEquals(HttpMethod::POST, $req->getMethod());
        $this->assertNotNull($req->getEntity());
        $this->assertEquals(MediaType::JSON->value, $req->getHeader('Content-Type'));
    }
}
