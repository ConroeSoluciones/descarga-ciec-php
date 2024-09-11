<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use InvalidArgumentException;

class Credenciales
{
    public function __construct(
        private readonly string $user,
        private readonly string $password,
    ) {
        if ($this->user === '') {
            throw new InvalidArgumentException('El usuario no puede ser vacio');
        }

        if ($this->password === '') {
            throw new InvalidArgumentException('El password no puede ser vacio');
        }
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
