<?php

namespace App\Http\Libs\Nomina\Facades;

use App\Models\IngresoNoPrestacional;
use App\Models\IngresoPrestacional;

use App\Http\Libs\Nomina\Calculos\CalculoIngresos;
use App\Http\Libs\Nomina\PeriodoPago;
use App\Models\BenefitIncome;
use App\Models\BenefitNotIncome;
use App\Models\Bonifications;
use App\Models\Person;

/**
 * Facade para el cálculo de los ingresos de un x funcionario en un determinado periodo de pago
 * para más información de los cálculos ver la clase CacluloIngresos que es la base de esta Facade
 */
class NominaIngresos extends PeriodoPago
{
    /**
     * Funcionario al cual se le calculan las novedades
     *
     * @var  App\Funcionario
     */
    protected static $funcionario;

    /**
     * Instancia de la clase CalculoIngresos
     *
     * @var App\Http\Libs\Nomina\Calculos\CalculoIngresos;
     */
    protected $calculoIngresos;

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
     * @return NominaIngesos
     */
    public static function ingresosFuncionarioWithId($id)
    {
        self::$funcionario = Person::findOrFail($id);
        return new self;
    }

    /**
     * Settear las fechas de inicio y fin del periodo  y crear objeto de CalculoIngresos con el funcionario y las fechas de inicio y fin del periodo, se utiliza queryScope de los modelos IngresoPrestacional e IngresoNoPrestacional, ver modelos para más información...
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return NominaIngresos
     */
    public function fromTo($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->calculoIngresos = new CalculoIngresos(
            BenefitIncome::obtener(self::$funcionario, $this->fechaInicio, $this->fechaFin)
                ->concat(BenefitNotIncome::obtener(self::$funcionario, $this->fechaInicio, $this->fechaFin))
                ->concat(Bonifications::obtener(self::$funcionario, $this->fechaInicio, $this->fechaFin))
        );
       
        return $this;
    }

    /**
     * Relizar el registro de los ingresos y a su vez calcular el valor total
     *
     * @return Collection
     */
    public function calculate()
    {
  
        $this->calculoIngresos->registrarIngresos();
    
        return $this->calculoIngresos->crearColeccion();
    }
}
