<?php

namespace App\Clases;

use App\Interfaces\PorcentajeInterface;

class Del100Al66 implements PorcentajeInterface
{

    public $objeParams;
    public $sumaSalario = 0;
    const PERIODODIAS = 30;
    const PUNTOQUIEBRE = 2;
    const PORCENTAJE = 66.67;
    const FACTORCONVERSION = 100;


    function __construct($objeParams)
    {
        $this->objeParams = $objeParams;
    }

    public function  calculateIngresoDeIncapacidad()
    {
        if ($this->objeParams->dias >  self::PUNTOQUIEBRE) {
            $this->sumaSalario +=   round($this->objeParams->salario * self::PUNTOQUIEBRE / self::PERIODODIAS);
            $this->sumaSalario +=  round((($this->objeParams->salario /  self::PERIODODIAS) * ($this->objeParams->dias - self::PUNTOQUIEBRE))  * self::PORCENTAJE) / self::FACTORCONVERSION;
            return  $this->sumaSalario;
        }
        $this->sumaSalario +=  round($this->objeParams->salario * $this->objeParams->dias  /  self::PERIODODIAS);
        return $this->sumaSalario;
    }
}
