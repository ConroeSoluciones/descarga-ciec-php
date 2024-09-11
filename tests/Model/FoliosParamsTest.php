<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model;

use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\FoliosParams;
use Csfacturacion\Descarga\Model\Uuid;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

use function json_encode;

use const JSON_PRETTY_PRINT;

class FoliosParamsTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $p = new FoliosParams(
            new Credenciales('AAA010101AAA', 'CIEC'),
            [
                new Uuid('a87c1d56-52f3-4680-a5cb-ddddb5786964'),
                new Uuid('451fabc1-7a87-43dc-a7b7-9c58d0c78624'),
            ],
        );

        $this->assertInstanceOf(JsonSerializable::class, $p);

        $expected = <<<'JSON'
        {
            "servicio": "CRAPI",
            "descarga": {
                "rfcContribuyente": "AAA010101AAA",
                "password": "CIEC",
                "folios": [
                    "a87c1d56-52f3-4680-a5cb-ddddb5786964",
                    "451fabc1-7a87-43dc-a7b7-9c58d0c78624"
                ]
            }
        }
        JSON;

        $this->assertEquals($expected, (string) json_encode($p, JSON_PRETTY_PRINT));
    }
}
