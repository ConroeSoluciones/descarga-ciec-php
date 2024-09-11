<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use Csfacturacion\Descarga\Model\Filter\CaseFilter;
use Csfacturacion\Descarga\Model\Filter\DocTypeFilter;
use Csfacturacion\Descarga\Model\Filter\StatusFilter;
use DateTimeInterface;
use JsonSerializable;

class Parameters implements JsonSerializable
{
    private ?Rfc $rfcBusqueda;

    private DateTimeInterface $fechaInicio;

    private DateTimeInterface $fechaFin;

    private StatusFilter $status;

    private CaseFilter $caso;

    private Servicio $servicio;

    private Credenciales $accesoSat;

    private DocTypeFilter $tipoDoc;

    public function __construct(ParametersBuilder $builder)
    {
        $this->rfcBusqueda = $builder->getRfcBusqueda();
        $this->fechaInicio = $builder->getFechaInicio();
        $this->fechaFin = $builder->getFechaFin();
        $this->status = $builder->getStatus();
        $this->caso = $builder->getCaso();
        $this->servicio = $builder->getServicio();
        $this->accesoSat = $builder->getAccesoSat();
        $this->tipoDoc = $builder->getTipoDoc();
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

    public function getServicio(): Servicio
    {
        return $this->servicio;
    }

    public function getAccesoSat(): Credenciales
    {
        return $this->accesoSat;
    }

    public function getTipoDoc(): DocTypeFilter
    {
        return $this->tipoDoc;
    }

    /**
     * @return array{
     *     servicio: string,
     *     descarga: array{
     *          rfcContribuyente: string,
     *          password: string,
     *          fechaInicio: string,
     *          fechaFin: string,
     *          tipo: string,
     *          tipoDoc: string,
     *          status: string,
     *          rfcBusqueda?: string
     *      }
     *     }
     */
    public function jsonSerialize(): array
    {
        $data = [
            'servicio' => $this->servicio->value,
            'descarga' => [
                'rfcContribuyente' => $this->accesoSat->getUser(),
                'password' => $this->accesoSat->getPassword(),
                'fechaInicio' => $this->fechaInicio->format('Y-m-d\TH:i:s'),
                'fechaFin' => $this->fechaFin->format('Y-m-d\TH:i:s'),
                'tipo' => $this->caso->value,
                'tipoDoc' => $this->tipoDoc->value,
                'status' => $this->status->value,
            ],
        ];

        if ($this->rfcBusqueda !== null) {
            $data['descarga']['rfcBusqueda'] = $this->rfcBusqueda->getValue();
        }

        return $data;
    }
}
