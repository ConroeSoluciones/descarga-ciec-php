<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model;

use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\ParametersBuilder;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

use function json_encode;

use const JSON_PRETTY_PRINT;

class ParametersTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $p = (new ParametersBuilder())
            ->accesoSat(new Credenciales('AAA010101AAA', 'CIEC'))
            ->build();

        $this->assertInstanceOf(JsonSerializable::class, $p);

        $expected = <<<JSON
        {
            "servicio": "CRAPI",
            "descarga": {
                "rfcContribuyente": "AAA010101AAA",
                "password": "CIEC",
                "fechaInicio": "{$p->getFechaInicio()->format('Y-m-d\TH:i:s')}",
                "fechaFin": "{$p->getFechaFin()->format('Y-m-d\TH:i:s')}",
                "tipo": "todas",
                "tipoDoc": "cfdi",
                "status": "todos"
            }
        }
        JSON;

        $this->assertEquals($expected, (string) json_encode($p, JSON_PRETTY_PRINT));
    }
}
