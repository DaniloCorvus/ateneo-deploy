<?php

namespace App\Clases;

use App\Interfaces\PorcentajeInterface;

class Al100 implements PorcentajeInterface
{

    public $objeParams;
    public $sumaSalario = 0;
    const PERIODODIAS = 30;

    function __construct($objeParams)
    {
        $this->objeParams = $objeParams;
    }

    public function  calculateIngresoDeIncapacidad()
    {
        if ($this->objeParams->suma == '0') {
            return 0;
        }
        $this->sumaSalario +=  round($this->objeParams->salario * $this->objeParams->dias  /  self::PERIODODIAS);
        return $this->sumaSalario;
    }
}
