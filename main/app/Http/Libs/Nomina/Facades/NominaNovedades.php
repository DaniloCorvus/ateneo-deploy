<?php

namespace App\Http\Libs\Nomina\Facades;

use App\Http\Libs\Nomina\PeriodoPago;
use App\Http\Libs\Nomina\Calculos\CalculoNovedades;

use App\Models\Novedad;
use App\Models\PayrollFactor;
use App\Models\Person;

/**
 * Facade para el cálculo de las novedades de un x funcionario en un determinado periodo de pago
 * para más información de los cálculos ver la clase CalculoNoveadaes que es la base de esta Facade
 */
class NominaNovedades extends PeriodoPago
{

    /**
     * Funcionario al cual se le calculan las novedades
     *
     * @var  App\Funcionario
     */
    protected static $funcionario;

    /**
     * Instancia de la clase CalculoNovedades
     *
     * @var App\Http\Libs\Nomina\Calculos\CalculoNovedades;
     */
    protected $calculoNovedades;

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
     * @return NominaNovedades
     */
    public static function novedadesFuncionarioWithId($id)
    {
        self::$funcionario = Person::with('contractultimate')->find($id);
        return new self;
    }


    /**
     * Settear las fechas de inicio y fin del periodo  y crear objeto de CalculoNovedades con el salario del funcionario y las fechas de inicio y fin del periodo
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return NominaNovedades
     */
    public function fromTo($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->calculoNovedades = new CalculoNovedades(self::$funcionario->contractultimate->salary, $this->fechaInicio, $this->fechaFin);
      
        return $this;
    }

    /**
     * Realizar el cálculo de las novedades que existen entre las fechas de inicio y fin de periodo, 
     * se utiliza queryScope del modelo Novedad (ver modelo para más información)
     *
     * @return void
     */
    public function calculate()
    {

       
        $this->calculoNovedades->existenVacaciones(
            PayrollFactor::vacations(self::$funcionario, $this->fechaInicio)
        );
     
        $this->calculoNovedades->existenNovedades(
            PayrollFactor::factors(self::$funcionario, $this->fechaFin)->get()
        );
        

        $this->calculoNovedades->totalizarNovedad()->calcularValorTotal();

        return $this->calculoNovedades->crearColeccion();
    }
}
