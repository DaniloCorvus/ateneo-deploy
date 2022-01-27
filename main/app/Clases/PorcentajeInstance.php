<?php

namespace App\Clases;

use InvalidArgumentException;

class PorcentajeInstance
{
    public $objeParams;

    function __construct($objeParams)
    {
        $this->objeParams = $objeParams;
        $this->getSalario();
    }

    public function getSalario()
    {
        switch ($this->objeParams->porcentaje) {
            case 1:
                return new Del100Al66($this->objeParams);
                break;
            case 2:
                return new Al66($this->objeParams);
                break;
            case 3:
                return new Al100($this->objeParams);
                break;
            default:
                throw new InvalidArgumentException('No existe este porcentaje de pago');
                break;
        }
    }
}
