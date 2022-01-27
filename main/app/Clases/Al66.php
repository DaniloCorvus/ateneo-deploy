<?php

namespace App\Clases;

use App\Interfaces\PorcentajeInterface;

class Al66 implements PorcentajeInterface
{

    public $objeParams;
    public $sumaSalario = 0;
    const PERIODODIAS = 30;
    const PORCENTAJE = 66.67;
    const FACTORCONVERSION = 100;

    function __construct($objeParams)
    {
        $this->objeParams = $objeParams;
    }

    public function  calculateIngresoDeIncapacidad()
    {
        $this->sumaSalario +=  round(($this->objeParams->salario * $this->objeParams->dias  /  self::PERIODODIAS) * self::PORCENTAJE) / SELF::FACTORCONVERSION;
        return $this->sumaSalario;
    }
}
