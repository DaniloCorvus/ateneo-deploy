<?php

namespace App\Http\Libs\Nomina\Facades;

use App\Models\Funcionario;
use App\Models\Empresa;
use App\Models\Novedad;

use App\Http\Libs\Nomina\Calculos\CalculoSalario;
use App\Http\Libs\Nomina\Calculos\CalculoNovedades;
use App\Http\Libs\Nomina\PeriodoPago;
use App\Models\Company;
use App\Models\PayrollFactor;
use App\Models\Person;

class NominaSalario extends PeriodoPago
{
    /**
     * Funcionario al cual se le calcula el salario
     *
     * @var  App\Funcionario
     */
    protected static $funcionario;
    static $auxilioTransporte;

    /**
     * Instancia de la clase CalculoSalario
     *
     * @var App\Http\Libs\Nomina\Calculos\CalculoSalario;
     */
    protected $calculoSalario;

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
     * @return NominaSalario
     */
     
     public function __construct(){
         $this->geAuxilioTransporte();
     }
     
     
    public static function salarioFuncionarioWithId($id)
    {
        $contratos = function ($q) {
            $q->where('liquidated', 0)->orderBy('id', 'Desc')->take(1);
        };
        self::$funcionario = Person::with('contractultimate')->with(['work_contracts' => $contratos])->findOrFail($id);
        return new self;
    }


    public static function geAuxilioTransporte()
    {
        self::$auxilioTransporte =  Company::first(['transportation_assistance'])['transportation_assistance'];
    }

    public function fromTo($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->verifiyNovedadesFromTo($this->fechaInicio, $this->fechaFin);
      
        $this->calculoSalario = new CalculoSalario(
            self::$funcionario->contractultimate->salary,
            $this->calculoNovedades->getDias(),
            $this->fechaInicio,
            $this->fechaFin
        );
        
        return $this;
    }

    public function verifiyNovedadesFromTo($fechaInicio, $fechaFin)
    {
        //*Aqui/
        $this->calculoNovedades = new CalculoNovedades(
            self::$funcionario->contractultimate->salary,
            $fechaInicio,
            $fechaFin
        );
        $this->calculoNovedades->existenVacaciones(
            PayrollFactor::vacations(self::$funcionario, $fechaInicio)
        );
       
        $this->calculoNovedades->existenNovedades(
            PayrollFactor::factors(self::$funcionario, $fechaFin)->get()
        );
    }

    public function calculate()
    {
        $this->calculoSalario->verificarFechasContrato(self::$funcionario->contractultimate->date_of_admission, self::$funcionario->contractultimate->date_end);
        
        $this->calculoSalario->valorSubsidioTransporte =  Company::first(['transportation_assistance'])['transportation_assistance'];
        
        $this->calculoSalario->calcularDiasTrabajados()->calcularSalarioDiasTrabajados();

        $this->calculoSalario->calcularTransporteDiasTrabajados(self::$funcionario->contractultimate->transport_subsidy);

        return $this->calculoSalario->crearColeccion();
    }
}
