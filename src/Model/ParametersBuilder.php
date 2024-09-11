<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use Csfacturacion\Descarga\Model\Filter\CaseFilter;
use Csfacturacion\Descarga\Model\Filter\DocTypeFilter;
use Csfacturacion\Descarga\Model\Filter\StatusFilter;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

class ParametersBuilder
{
    private ?Rfc $rfcBusqueda;

    private DateTimeInterface $fechaInicio;

    private DateTimeInterface $fechaFin;

    private StatusFilter $status;

    private CaseFilter $caso;

    private Servicio $servicio;

    private ?Credenciales $accesoSat;

    private DocTypeFilter $tipoDoc;

    /**
     * Crea una nueva instancia
     */
    public function __construct()
    {
        // defaults
        $this->accesoSat = null;
        $this->rfcBusqueda = null;
        $this->fechaInicio = new DateTime('now');
        $this->fechaFin = new DateTime('now');
        $this->servicio = Servicio::API_CIEC;
        $this->status = StatusFilter::TODOS;
        $this->caso = CaseFilter::TODAS;
        $this->tipoDoc = DocTypeFilter::CFDI;
    }

    public function accesoSat(Credenciales $accesoSat): self
    {
        $this->accesoSat = $accesoSat;

        return $this;
    }

    public function rfcBusqueda(Rfc $rfc): self
    {
        $this->rfcBusqueda = $rfc;

        return $this;
    }

    public function fechaInicio(DateTimeInterface $fecha): self
    {
        $this->fechaInicio = $fecha;

        return $this;
    }

    public function fechaFin(DateTimeInterface $fecha): self
    {
        $this->fechaFin = $fecha;

        return $this;
    }

    public function status(StatusFilter $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function caso(CaseFilter $caso): self
    {
        $this->caso = $caso;

        return $this;
    }

    public function servicio(Servicio $servicio): self
    {
        $this->servicio = $servicio;

        return $this;
    }

    public function tipoDoc(DocTypeFilter $tipoDoc): self
    {
        $this->tipoDoc = $tipoDoc;

        return $this;
    }

    private function validate(): void
    {
        $now = new DateTime('now');

        if ($this->accesoSat === null) {
            throw new InvalidArgumentException('El acceso al SAT debe ser especificado');
        }

        if ($this->fechaInicio > $this->fechaFin) {
            throw new LogicException('fechaInicio no puede ser mayor que fechaFin');
        }

        if ($this->fechaInicio > $now || $this->fechaFin > $now) {
            throw new LogicException('Las fechas no pueden ser en el futuro!');
        }
    }

    /**
     * Construir una instancia de Parametros.
     */
    public function build(): Parameters
    {
        $this->validate();

        return new Parameters($this);
    }

    public function getServicio(): Servicio
    {
        return $this->servicio;
    }

    public function getRfcBusqueda(): ?Rfc
    {
        return $this->rfcBusqueda;
    }

    public function getFechaInicio(): DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function getFechaFin(): DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function getStatus(): StatusFilter
    {
        return $this->status;
    }

    public function getCaso(): CaseFilter
    {
        return $this->caso;
    }

    public function getAccesoSat(): Credenciales
    {
        if ($this->accesoSat === null) {
            throw new RuntimeException('Se debe especificar la credencial SAT');
        }

        return $this->accesoSat;
    }

    public function getTipoDoc(): DocTypeFilter
    {
        return $this->tipoDoc;
    }
}
