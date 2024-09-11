<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use DateTimeInterface;
use LogicException;
use RuntimeException;

/**
 * Builder instance for {@see CfdiMeta}.
 *
 * @internal
 */
class CfdiMetaBuilder
{
    private ?Uuid $folio = null;

    private ?Contribuyente $emisor = null;

    private ?Contribuyente $receptor = null;

    private ?DateTimeInterface $fechaEmision = null;

    private ?DateTimeInterface $fechaCertificacion = null;

    private ?Contribuyente $pac = null;

    private ?float $total = null;

    private ?string $tipo = null;

    private ?CfdiStatus $status = null;

    private ?bool $hasXmlFile = null;

    public function withFolio(Uuid $folio): CfdiMetaBuilder
    {
        $this->folio = $folio;

        return $this;
    }

    public function withEmisor(Contribuyente $emisor): CfdiMetaBuilder
    {
        $this->emisor = $emisor;

        return $this;
    }

    public function withReceptor(Contribuyente $receptor): CfdiMetaBuilder
    {
        $this->receptor = $receptor;

        return $this;
    }

    public function withFechaEmision(DateTimeInterface $fechaEmision): CfdiMetaBuilder
    {
        $this->fechaEmision = $fechaEmision;

        return $this;
    }

    public function withFechaCertificacion(DateTimeInterface $fechaCertificacion): CfdiMetaBuilder
    {
        $this->fechaCertificacion = $fechaCertificacion;

        return $this;
    }

    public function withPac(Contribuyente $pac): CfdiMetaBuilder
    {
        $this->pac = $pac;

        return $this;
    }

    public function withTotal(float $total): CfdiMetaBuilder
    {
        $this->total = $total;

        return $this;
    }

    public function withTipo(string $tipo): CfdiMetaBuilder
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function withStatus(CfdiStatus $status): CfdiMetaBuilder
    {
        $this->status = $status;

        return $this;
    }

    public function withHasXmlFile(bool $hasXmlFile): CfdiMetaBuilder
    {
        $this->hasXmlFile = $hasXmlFile;

        return $this;
    }

    private function validate(): void
    {
        $requiredFields = [
            'folio', 'emisor', 'receptor', 'fechaEmision', 'fechaCertificacion',
            'pac', 'total', 'tipo', 'status', 'hasXmlFile',
        ];

        foreach ($requiredFields as $field) {
            if ($this->$field === null || $this->$field === '') {
                throw new LogicException("$field no puede ser null o vacio!");
            }
        }
    }

    /**
     * @return CfdiMeta New instance from Builder.
     *
     * @throws LogicException if Builder does not validate.
     */
    public function build(): CfdiMeta
    {
        $this->validate();

        return new CfdiMeta($this);
    }

    public function getFolio(): Uuid
    {
        if ($this->folio === null) {
            throw new RuntimeException('emisor must be not null');
        }

        return $this->folio;
    }

    public function getEmisor(): Contribuyente
    {
        if ($this->emisor === null) {
            throw new RuntimeException('emisor must be not null');
        }

        return $this->emisor;
    }

    public function getReceptor(): Contribuyente
    {
        if ($this->receptor === null) {
            throw new RuntimeException('receptor must be not null');
        }

        return $this->receptor;
    }

    public function getFechaEmision(): DateTimeInterface
    {
        if ($this->fechaEmision === null) {
            throw new RuntimeException('fechaEmision must be not null');
        }

        return $this->fechaEmision;
    }

    public function getFechaCertificacion(): DateTimeInterface
    {
        if ($this->fechaCertificacion === null) {
            throw new RuntimeException('fechaCertificacion must be not null');
        }

        return $this->fechaCertificacion;
    }

    public function getPac(): Contribuyente
    {
        if ($this->pac === null) {
            throw new RuntimeException('pac must be not null');
        }

        return $this->pac;
    }

    public function getTotal(): float
    {
        if ($this->total === null) {
            throw new RuntimeException('total must be not null');
        }

        return $this->total;
    }

    public function getTipo(): string
    {
        if ($this->tipo === null) {
            throw new RuntimeException('tipo must be not null');
        }

        return $this->tipo;
    }

    public function getStatus(): CfdiStatus
    {
        if ($this->status === null) {
            throw new RuntimeException('status must be not null');
        }

        return $this->status;
    }

    public function hasXmlFile(): bool
    {
        if ($this->hasXmlFile === null) {
            throw new RuntimeException('hasXmlFile must be not null');
        }

        return $this->hasXmlFile;
    }
}
