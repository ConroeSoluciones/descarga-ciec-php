<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model;

use Csfacturacion\Descarga\Model\CfdiMeta;
use Csfacturacion\Descarga\Model\CfdiMetaBuilder;
use Csfacturacion\Descarga\Model\CfdiStatus;
use Csfacturacion\Descarga\Model\Contribuyente;
use Csfacturacion\Descarga\Model\Uuid;
use Csfacturacion\Descarga\Util\Deserializable;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CfdiMetaTest extends TestCase
{
    public function testFromJson(): void
    {
        $raw = <<<'JSON'
        {
            "folio": "20354d7a-e4fe-47af-8ff6-187bca92f3f9",
            "emisor": {
                "razonSocial": "ACME SA DE CV",
                "rfc": "AAA010101AAA"
            },
            "receptor": {
                "razonSocial": "ACME SA DE CV",
                "rfc": "AAA010101AAA"
            },
            "fechaEmision": "2022-10-20T15:28:42.000-05:00",
            "fechaCertificacion": "2022-10-20T15:28:56.000-05:00",
            "PACCertificador": {
                "rfc": "MAS0810247C0"
            },
            "total": 4699.40,
            "tipo": "NOMINA",
            "status": "VIGENTE",
            "cancelacion": {
                "cancelable": "Cancelable sin aceptación"
            },
            "url": "5978/9181/20354d7a-e4fe-47af-8ff6-187bca92f3f9.xml"
        }
        JSON;

        /**@var CfdiMeta $cfdi */
        $cfdi = CfdiMeta::fromJson($raw);

        $expected = (new CfdiMetaBuilder())
            ->withFolio(new Uuid('20354d7a-e4fe-47af-8ff6-187bca92f3f9'))
            ->withFechaCertificacion($this->crateDatetime('2022-10-20T15:28:56.000-05:00'))
            ->withFechaEmision($this->crateDatetime('2022-10-20T15:28:42.000-05:00'))
            ->withStatus(CfdiStatus::VIGENTE)
            ->withPac(new Contribuyente('MAS0810247C0', 'NA'))
            ->withReceptor(new Contribuyente('AAA010101AAA', 'ACME SA DE CV'))
            ->withEmisor(new Contribuyente('AAA010101AAA', 'ACME SA DE CV'))
            ->withTotal(4699.40)
            ->withHasXmlFile(true)
            ->withTipo('NOMINA')
            ->build();

        $this->assertInstanceOf(Deserializable::class, $cfdi);

        $this->assertEquals($expected, $cfdi);
    }

    private function crateDatetime(string $datetime): DateTimeInterface
    {
        $d = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $datetime);
        if ($d === false) {
            throw new InvalidArgumentException('Invalid datetime format: ' . $datetime);
        }

        return $d;
    }
}
