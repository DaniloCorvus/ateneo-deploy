<?php

namespace App\Http\Libs\Nomina\Facades;

use App\Http\Libs\Nomina\Calculos\CalculoDeducciones;
use App\Http\Libs\Nomina\PeriodoPago;

use App\Models\Funcionario;
use App\Models\Deduccion;
use App\Models\Deduction;
use App\Models\Person;

class NominaDeducciones extends PeriodoPago
{
    /**
     * Funcionario al cual se le calculan las retenciones
     *
     * @var  App\Funcionario
     */
    protected static $funcionario;

    /**
     * Instancia de la clase CalculoDeducciones
     *
     * @var App\Http\Libs\Nomina\Calculos\CalculoDeducciones
     */
    protected $calculoDeducciones;

    /**
     * Fecha de inicio del periodo de pago
     *
     * @var string
     */
    protected $fechaInicio;

    /**
     * Fecha de fin del periodo de pago
     *
     * @var string
     */
    protected $fechaFin;

    /**
     * Settea la propiedad funcionario filtrando al funcionario que se pase por el parámetro $id,
     * retorna una nueva instancia de la clase 
     *
     * @param integer $id
     * @return NominaDeducciones
     */
    public static function deduccionesFuncionarioWithId($id)
    {
        self::$funcionario = Person::find($id);
        return new self;
    }

    public function fromTo($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;

        $this->calculoDeducciones = new CalculoDeducciones(
            Deduction::periodo(self::$funcionario, $this->fechaInicio, $this->fechaFin)
        );
        
        return $this;
    }

    public function calculate()
    {

        $this->calculoDeducciones->calcularTotalDeducciones();
        

        return $this->calculoDeducciones->crearColeccion();
    }
}
