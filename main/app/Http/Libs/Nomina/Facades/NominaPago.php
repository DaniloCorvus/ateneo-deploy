<?php

namespace App\Http\Libs\Nomina\Facades;

use App\Models\Funcionario;

use App\Http\Libs\Nomina\Calculos\ResumenPago;
use App\Http\Libs\Nomina\PeriodoPago;
use App\Models\Person;

class NominaPago extends PeriodoPago
{
    /**
     * Funcionario al cual se le calcula el salario
     *
     * @var  App\Funcionario
     */
    protected static $funcionario;

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
     * Facade NominaSalario
     *
     * @var NominaSalario
     */
    protected $facadeSalario;
    /**
     * Facade NominaExtras
     *
     * @var NominaExtras
     */
    protected $facadeExtras;

    /**
     * Facade Novedades
     *
     * @var NominaNovedades
     */
    protected $facadeNovedades;

    /**
     * Facade NominaIngresos
     *
     * @var NominaIngresos
     */
    protected $facadeIngresos;

    /**
     * Facade Retenciones
     *
     * @var NominaRetenciones
     */
    protected $facadeRetenciones;

    /**
     * Facade NominaDeducciones
     *
     * @var NominaDeducciones
     */
    protected $facadeDeducciones;


    /**
     * Instancia clase ResumenPago
     *
     * @var ResumenPago
     */
    protected $resumenPago;

    /**
     * Settea la propiedad funcionario filtrando al funcionario que se pase por el parámetro $id,
     * retorna una nueva instancia de la clase
     *
     * @param integer $id
     * @return NominaSalario
     */
    public static function pagoFuncionarioWithId($id)
    {
        self::$funcionario = Person::findOrFail($id);
        return new self;
    }


    public function fromTo($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;

        $this->facadeSalario = NominaSalario::salarioFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin)
            ->calculate();



        $this->facadeExtras = NominaExtras::extrasFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin);



        $this->facadeNovedades = NominaNovedades::novedadesFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin)
            ->calculate();

        $this->facadeIngresos = NominaIngresos::ingresosFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin)
            ->calculate();


        $this->facadeRetenciones = NominaRetenciones::retencionesFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin)
            ->calculate();


        $this->facadeDeducciones = NominaDeducciones::deduccionesFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin)
            ->calculate();

        return $this;
    }


    public function calculate()
    {

        $this->resumenPago =
            new ResumenPago(
                $this->facadeSalario['salary'],
                $this->facadeSalario['transportation_assistance'],
                $this->facadeExtras['valor_total'],
                $this->facadeNovedades['valor_total'],
                $this->facadeIngresos['valor_total'],
                $this->facadeRetenciones['valor_total'],
                $this->facadeDeducciones['valor_total']
            );
        $this->resumenPago->calculo();

        return $this->resumenPago->crearColeccion();
    }

    public function customCalculate()
    {
        $this->resumenPago =
            new ResumenPago($this->facadeSalario['salario'], $this->facadeSalario['auxilio_transporte'], $this->facadeExtras['valor_total'], $this->facadeNovedades['valor_total'], $this->facadeIngresos['valor_total'], $this->facadeRetenciones['valor_total'], $this->facadeDeducciones['valor_total']);

        $this->resumenPago->calculo();

        return $this->resumenPago->crearColeccionCustom();
    }
}
