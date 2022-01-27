<?php

namespace App\Http\Libs\Nomina\Facades;

use App\Models\Funcionario;
use App\Models\Empresa;

use App\Http\Libs\Nomina\Calculos\CalculoSeguridad;
use App\Http\Libs\Nomina\PeriodoPago;
use App\Models\Company;
use App\Models\Person;

class NominaSeguridad extends PeriodoPago
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
     * Facade NominaRetenciones
     *
     * @var NominaRetenciones
     */
    protected $facadeRetenciones;

    /**
     * Facade NominaNovedades
     *
     * @var NominaNovedades
     */
    protected $facadeNovedades;

    /**
     * Instancia CalculoSeguridad
     *
     * @var CalculoSeguridad
     */
    protected $calculoSeguridad;
    protected static $empresa;

    /**
     * Settea la propiedad funcionario filtrando al funcionario que se pase por el parámetro $id,
     * retorna una nueva instancia de la clase 
     *
     * @param integer $id
     * @return NominaSalario
     */
    public static function seguridadFuncionarioWithId($id)
    {
        self::$funcionario = Person::with('contractultimate')->findOrFail($id);
        self::$empresa = Company::first();
        return new self;
    }

    public function fromTo($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;

        $this->facadeRetenciones = NominaRetenciones::retencionesFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin)
            ->calculate();
    
        $this->facadeNovedades = NominaNovedades::novedadesFuncionarioWithId(self::$funcionario->id)
            ->fromTo($this->fechaInicio, $this->fechaFin)
            ->calculate();
       

        return $this;
    }

    public function calculate()
    {
        $salarioMinimo = self::$empresa['base_salary'];
      
        $this->calculoSeguridad = new CalculoSeguridad(
            $this->facadeRetenciones['IBC_seguridad'],
            $this->facadeRetenciones['retenciones']['Salario'],
            self::$funcionario->contractultimate->salary,
            $this->facadeRetenciones['retenciones']['Horas extras'],
            $this->facadeRetenciones['retenciones']['Ingresos'],
            $this->facadeNovedades['novedades_totales']['Vacaciones'] ?? 0
        );
        
        $this->calculoSeguridad->calcularIbcRiesgos($salarioMinimo)->calcularIbcParafiscales();
        
        $this->calculoSeguridad->calcularPension()->calcularSalud(self::$empresa['law_1607'], $salarioMinimo)
            ->calcularRiesgos(self::$funcionario);

        $this->calculoSeguridad
            ->calcularSena(self::$empresa['law_1607'], $salarioMinimo)
            ->calcularIcbf(self::$empresa['law_1607'], $salarioMinimo)
            ->calcularCajaCompensacion();
        
        $this->calculoSeguridad
            ->calcularTotalSeguridadSocial()
            ->calcularTotalParafiscales();
           
        $this->calculoSeguridad->calcularTotal();
       
        return $this->calculoSeguridad->crearColeccion();
    }
}
