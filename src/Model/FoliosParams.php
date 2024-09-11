<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use JsonSerializable;

class FoliosParams implements JsonSerializable
{
    /**
     * @param Uuid[] $foliosList
     */
    public function __construct(
        private readonly Credenciales $accesoSat,
        private readonly array $foliosList,
        private readonly Servicio $servicio = Servicio::API_CIEC,
    ) {
    }

    public function getServicio(): Servicio
    {
        return $this->servicio;
    }

    public function getAccesoSat(): Credenciales
    {
        return $this->accesoSat;
    }

    /**
     * @return Uuid[]
     */
    public function getFoliosList(): array
    {
        return $this->foliosList;
    }

    /**
     * @return array{
     *     servicio: string,
     *     descarga: array{
     *          rfcContribuyente: string,
     *          password: string,
     *          folios: Uuid[]
     *      }
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'servicio' => $this->servicio->value,
            'descarga' => [
                'rfcContribuyente' => $this->accesoSat->getUser(),
                'password' => $this->accesoSat->getPassword(),
                'folios' => $this->foliosList,
            ],
        ];
    }
}
