<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use Csfacturacion\Descarga\Util\Deserializable;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use JsonException;

use function is_list;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class CfdiMeta implements Deserializable
{
    private Uuid $folio;

    private Contribuyente $emisor;

    private Contribuyente $receptor;

    private DateTimeInterface $fechaEmision;

    private DateTimeInterface $fechaCertificacion;

    private Contribuyente $pac;

    private float $total;

    private string $tipo;

    private CfdiStatus $status;

    private bool $hasXmlFile;

    public function __construct(CfdiMetaBuilder $builder)
    {
        $this->folio = $builder->getFolio();
        $this->emisor = $builder->getEmisor();
        $this->receptor = $builder->getReceptor();
        $this->fechaEmision = $builder->getFechaEmision();
        $this->fechaCertificacion = $builder->getFechaCertificacion();
        $this->pac = $builder->getPac();
        $this->total = $builder->getTotal();
        $this->tipo = $builder->getTipo();
        $this->status = $builder->getStatus();
        $this->hasXmlFile = $builder->hasXmlFile();
    }

    public function getFolio(): Uuid
    {
        return $this->folio;
    }

    public function getEmisor(): Contribuyente
    {
        return $this->emisor;
    }

    public function getReceptor(): Contribuyente
    {
        return $this->receptor;
    }

    public function getFechaEmision(): DateTimeInterface
    {
        return $this->fechaEmision;
    }

    public function getFechaCertificacion(): DateTimeInterface
    {
        return $this->fechaCertificacion;
    }

    public function getPac(): Contribuyente
    {
        return $this->pac;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getStatus(): CfdiStatus
    {
        return $this->status;
    }

    public function hasXmlFile(): bool
    {
        return $this->hasXmlFile;
    }

    /**
     * @return CfdiMeta|CfdiMeta[]
     *
     * @throws JsonException
     */
    public static function fromJson(string $raw): CfdiMeta | array
    {
        $data = (array) json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $isList = is_list($data);
        $dataList = $isList ? $data : [$data];
        /** @var CfdiMeta[] $models */
        $models = [];

        /**
         * @var array<string, mixed> $meta
         * @psalm-suppress MixedArgument
         */
        foreach ($dataList as $meta) {
            /** @psalm-suppress ArgumentTypeCoercion */
            $models[] = self::fromArray($meta); // @phpstan-ignore argument.type
        }

        return $isList ? $models : $models[0];
    }

    /**
     * @param array{
     *     'folio': string,
     *     'emisor': array{
     *         'rfc': string,
     *         'razonSocial': string
     *     },
     *     'receptor': array{
     *          'rfc': string,
     *          'razonSocial': string
     *      },
     *     'fechaEmision': string,
     *     'fechaCertificacion': 'string',
     *     'PACCertificador': array{
     *         'rfc': string
     *     },
     *     'total': float,
     *     'tipo': string,
     *     'status': string,
     *     'url': string|null
     * } $attrs
     *
     * @throws JsonException
     */
    private static function fromArray(array $attrs): CfdiMeta
    {
        $fe = DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $attrs['fechaEmision']);
        if ($fe === false) {
            throw new InvalidArgumentException('fechaEmision es invalido');
        }
        $fc = DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $attrs['fechaCertificacion']);
        if ($fc === false) {
            throw new InvalidArgumentException('fechaCertificacion es invalido');
        }

        return (new CfdiMetaBuilder())
            ->withFolio(new Uuid($attrs['folio']))
            ->withEmisor(new Contribuyente(
                $attrs['emisor']['rfc'],
                $attrs['emisor']['razonSocial'],
            ))->withReceptor(new Contribuyente(
                $attrs['receptor']['rfc'],
                $attrs['receptor']['razonSocial'],
            ))->withFechaEmision($fe)
            ->withFechaCertificacion($fc)
            ->withPac(new Contribuyente(new Rfc($attrs['PACCertificador']['rfc']), 'NA'))
            ->withTotal($attrs['total'])
            ->withTipo($attrs['tipo'])
            ->withStatus(CfdiStatus::from($attrs['status']))
            ->withHasXmlFile($attrs['url'] !== null && $attrs['url'] !== '')
            ->build();
    }
}
