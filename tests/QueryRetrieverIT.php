<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga;

use Csfacturacion\Descarga\Error\NotEnoughResultsError;
use Csfacturacion\Descarga\Error\QueryNotReadyYetError;
use Csfacturacion\Descarga\Model\Status;
use Csfacturacion\Descarga\Model\Summary;
use Csfacturacion\Descarga\Model\Uuid;
use Csfacturacion\Descarga\QueryRetriever;
use Csfacturacion\Descarga\QueryRetrieverApi;
use Csfacturacion\Descarga\Util\HttpClient;
use Csfacturacion\Descarga\Util\RequestFactory;

use function in_array;
use function sys_get_temp_dir;

class QueryRetrieverIT extends TestCase
{
    private const PAGE_SIZE = 20;

    private QueryRetrieverApi $queryRetriever;

    /**
     * @var array{'query_id': string, 'cfdi_folio': string, 'rfc': string, 'ciec': string}
     */
    private array $secrets;

    protected function setUp(): void
    {
        parent::setUp();
        $this->secrets = $this->getSecrets();
        $this->queryRetriever = new QueryRetriever(
            new Uuid($this->secrets['query_id']),
            new HttpClient(),
            new RequestFactory(),
        );
    }

    public function testGetProgress(): void
    {
        $p = $this->queryRetriever->getProgress();
        $this->assertTrue(in_array($p->getStatus(), Status::cases()));
    }

    public function testGetSummary(): Summary
    {
        $s = $this->queryRetriever->getSummary();
        $this->assertTrue($s->getTotal() >= 0);
        $this->assertTrue($s->getPages() >= 0);

        return $s;
    }

    /**
     * @throws NotEnoughResultsError
     * @throws QueryNotReadyYetError
     *
     * @depends testGetSummary
     */
    public function testGetResults(Summary $s): void
    {
        if ($s->getPages() > 0) {
            $r = $this->queryRetriever->getResults(1);
            $this->assertCount(self::PAGE_SIZE, $r);
        }
    }

    public function testGetXml(): void
    {
        $xml = $this->queryRetriever->getXml(new Uuid($this->secrets['cfdi_folio']));
        $this->assertNotEmpty($xml);
    }

    public function testAsZip(): void
    {
        $where = sys_get_temp_dir() . '/gml.zip';
        $this->queryRetriever->asZip($where);

        $this->assertFileExists($where);
    }
}
