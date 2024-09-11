<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model;

use Csfacturacion\Descarga\Model\Summary;
use Csfacturacion\Descarga\Util\Deserializable;
use PHPUnit\Framework\TestCase;

class SummaryTest extends TestCase
{
    public function testFromJson(): void
    {
        $raw = <<<'JSON'
        {
            "total": 67,
            "paginas": 4,
            "fechasMismoHorario": [],
            "xmlFaltantes": false,
            "cancelados": 0
        }
        JSON;

        $s = Summary::fromJson($raw);

        $this->assertInstanceOf(Deserializable::class, $s);
        $this->assertEquals(67, $s->getTotal());
        $this->assertEquals(4, $s->getPages());
        $this->assertFalse($s->hasMissingXml());
        $this->assertEquals(0, $s->getCancelados());
    }
}
