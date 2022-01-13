<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Support\Collection;


class ResumenPago implements Coleccion
{
    protected $valorSalario;
    protected $valorAuxilioTransporte;
    protected $valorHorasExtras;
    protected $valorNovedades;
    protected $valorIngresosAdicionales;
    protected $valorRetenciones;
    protected $valorDeducciones;
    protected $totalNeto;

    public function __construct(
        $valorSalario,
        $valorAuxilioTransporte,
        $valorHorasExtras,
        $valorNovedades,
        $valorIngresosAdicionales,
        $valorRetenciones,
        $valorDeducciones
    ) {
        $this->valorSalario = $valorSalario;
        $this->valorAuxilioTransporte = $valorAuxilioTransporte;
        $this->valorHorasExtras = $valorHorasExtras;
        $this->valorNovedades = $valorNovedades;
        $this->valorIngresosAdicionales = $valorIngresosAdicionales;
        $this->valorRetenciones = $valorRetenciones;
        $this->valorDeducciones = $valorDeducciones;
    }

    public function calculo()
    {
        $this->totalNeto = $this->valorSalario + $this->valorAuxilioTransporte +  $this->valorHorasExtras + $this->valorNovedades + $this->valorIngresosAdicionales;

        $this->totalNeto -= $this->valorRetenciones + $this->valorDeducciones;
    }

    public function getTotalNeto()
    {
        return $this->totalNeto;
    }

    public function crearColeccion()
    {
        return new Collection([
            'total_valor_neto' => $this->getTotalNeto()
        ]);
    }
    public function crearColeccionCustom()
    {
        return new Collection([
            'valorSalario' =>   $this->valorSalario,
            'valorAuxilioTransporte' =>  $this->valorAuxilioTransporte,
            'valorHorasExtras' =>  $this->valorHorasExtras,
            'valorNovedades' =>  $this->valorNovedades,
            'valorIngresosAdicionales' =>  $this->valorIngresosAdicionales,
            'valorRetenciones' =>  $this->valorRetenciones,
            'valorDeducciones' =>  $this->valorDeducciones,
        ]);
    }
}
