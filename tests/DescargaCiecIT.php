<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga;

use Csfacturacion\Descarga\DescargaCiec;
use Csfacturacion\Descarga\DescargaCiecApi;
use Csfacturacion\Descarga\Error\InvalidQueryError;
use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\Filter\CaseFilter;
use Csfacturacion\Descarga\Model\Filter\DocTypeFilter;
use Csfacturacion\Descarga\Model\Filter\StatusFilter;
use Csfacturacion\Descarga\Model\FoliosParams;
use Csfacturacion\Descarga\Model\ParametersBuilder;
use Csfacturacion\Descarga\Model\Uuid;
use DateTimeImmutable;

use function flush;
use function ob_flush;
use function printf;
use function sleep;

use const PHP_EOL;

class DescargaCiecIT extends TestCase
{
    private DescargaCiecApi $descargaCiec;

    private Credenciales $accesoSat;

    protected function setUp(): void
    {
        parent::setUp();
        $access = $this->getSecrets();
        $this->accesoSat = new Credenciales($access['rfc'], $access['ciec']);
        $accesoCs = new Credenciales($access['cs']['rfc'], $access['cs']['password']);
        $this->descargaCiec = new DescargaCiec($accesoCs);
    }

    public function testMakeQuery(): Uuid
    {
        $params = (new ParametersBuilder())
            ->accesoSat($this->accesoSat)
            ->tipoDoc(DocTypeFilter::CFDI)
            ->caso(CaseFilter::RECIBIDAS)
            ->fechaInicio(new DateTimeImmutable('first day of January 2024'))
            ->fechaFin(new DateTimeImmutable('last day of January 2024'))
            ->status(StatusFilter::CANCELADO)
            ->build();

        $q = $this->descargaCiec->makeQuery($params);

        while (!$q->isFinished()) {
            printf('consultando status: %s %s', $q->getProgress()->getStatus()->name, PHP_EOL);
            ob_flush(); // Limpia cualquier contenido en el buffer de PHP
            flush();
            sleep(1);
        }

        $this->assertTrue($q->isFinished());

        return $q->getFolio();
    }

    public function testMakeFoliosQuery(): Uuid
    {
        $p = new FoliosParams(
            $this->accesoSat,
            [
                new Uuid('f12020da-df6d-44d4-b80a-73595bfe256b'),
                new Uuid('fb4cb129-ea88-47a9-a70a-eb98d3ad9ea1'),
            ],
        );
        $q = $this->descargaCiec->byFolios($p);

        while (!$q->isFinished()) {
            printf('consultando status: %s %s', $q->getProgress()->getStatus()->name, PHP_EOL);
            ob_flush(); // Limpia cualquier contenido en el buffer de PHP
            flush();
            sleep(1);
        }

        $this->assertTrue($q->isFinished());

        return $q->getFolio();
    }

    public function testThrowInvalidQueryErrorWhenInvalidData(): void
    {
        $this->expectException(InvalidQueryError::class);
        $this->expectExceptionMessage('401');

        $params = (new ParametersBuilder())
            ->accesoSat($this->accesoSat)
            ->tipoDoc(DocTypeFilter::CFDI)
            ->caso(CaseFilter::RECIBIDAS)
            ->fechaInicio(new DateTimeImmutable('first day of January 2024'))
            ->fechaFin(new DateTimeImmutable('last day of January 2024'))
            ->status(StatusFilter::CANCELADO)
            ->build();

        $descargaCiec = new DescargaCiec(new Credenciales('BBB010101BBB', 'foo_bar'));
        $descargaCiec->makeQuery($params);
    }

    public function testSearch(): void
    {
        $s = $this->getSecrets();
        $q = $this->descargaCiec->search(new Uuid($s['query_id']));

        $this->assertTrue($q->isFinished());
    }

    public function testThrowInvalidQueryErrorWhenUuidDoesNotExist(): void
    {
        $this->expectException(InvalidQueryError::class);
        $this->descargaCiec->search(new Uuid('caa8530a-e555-442e-97e6-3c511cadd714'));
    }
}
