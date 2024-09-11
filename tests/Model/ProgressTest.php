<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model;

use Csfacturacion\Descarga\Model\Progress;
use Csfacturacion\Descarga\Model\Status;
use Csfacturacion\Descarga\Util\Deserializable;
use PHPUnit\Framework\TestCase;

class ProgressTest extends TestCase
{
    public function testFromJson(): void
    {
        $raw = <<<'JSON'
        {
            "estado": "COMPLETADO",
            "encontrados": 67
        }
        JSON;

        $p = Progress::fromJson($raw);

        $this->assertInstanceOf(Deserializable::class, $p);
        $this->assertEquals(67, $p->getFound());
        $this->assertEquals(Status::COMPLETADO, $p->getStatus());
    }
}
