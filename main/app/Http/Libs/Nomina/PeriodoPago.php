<?php

namespace App\Http\Libs\Nomina;

abstract class PeriodoPago
{
    abstract public function fromTo($fechaInicio, $fechaFin);
}
