<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Support\Collection;

/**
 * Clase para el cálculo de las retenciones  y el IBC
 */
class CalculoRetenciones implements Coleccion
{
    use Porcentaje;

    protected $retenciones = [];
    protected $ibcSeguridad;
    protected $salud;
    protected $uvt;
    protected $pension;
    protected $fondoSolidaridad;
    protected $fondoSubsistencia;
    protected $retencionFuente;
    protected $salarioSMLV;
    protected $salarioFuncionario;

    /**
     * Constructor
     *
     * @param int $salarioSMLV
     * @param int $salarioFuncionario
     */
    public function __construct($salarioSMLV, $salarioFuncionario)
    {
        $this->uvt=36308;
        $this->salarioSMLV = $salarioSMLV;
        $this->salarioFuncionario = $salarioFuncionario;
        $this->salud = 0;
        $this->pension = 0;
        $this->fondoSolidaridad = 0;
        $this->fondoSubsistencia = 0;
        $this->ibcSeguridad = 0;
    }

    /**
     * Registrar clave-valor en el container de retenciones
     *
     * @param string $llave
     * @param int $valor
     * @return void
     */
    public function registrar($llave, $valor)
    {
        $this->retenciones[$llave] = $valor;
    }

    /**
     * Registro masivo en el container
     *
     * @param array $items
     * @return void
     */
    public function registrarNovedades($items = [])
    {
        foreach ($items as $item => $valor) {
            $this->registrar($item, $valor);
        }
    }

    /**
     * Registro masivo ingressos en container de ingresos - clave-valor
     *
     * @param array $items
     * @return void
     */
    public function registrarIngresos($items = [])
    {
        $valorIngreso = 0;
        foreach ($items as $item => $valor) {
            $valorIngreso += $valor;
        }
        $this->registrar('Ingresos', $valorIngreso);
    }

    public function getRetenciones()
    {
        return $this->retenciones;
    }

    public function getIbcSeguridad()
    {
        return $this->ibcSeguridad;
    }

    public function calcularIbcSeguridad()
    {
        //dd($this->retenciones);
        foreach ($this->retenciones as $retencion => $valor) { 
            $this->ibcSeguridad += $valor;
        }
    }

    public function calculoIBCSalud()
    {
        $this->salud = round($this->ibcSeguridad * $this->porcentajeSaludFunc());
        return $this;
    }

    public function calculoIBCPension()
    {
        $this->pension = round($this->ibcSeguridad * $this->porcentajePensionFunc());
        return $this;
    }

    public function getSalud()
    {
        return $this->salud;
    }

    public function getPension()
    {
        return $this->pension;
    }

    public function getIBCRetenciones()
    {
        return $this->salud + $this->pension + $this->fondoSolidaridad + $this->fondoSubsistencia + $this->retencionFuente;
    }

    public function calculoIBCFondoSolidaridad()
    {
        if ($this->salarioFuncionario > $this->salarioSMLV * 4) {
            $this->fondoSolidaridad = round($this->ibcSeguridad * $this->porcentajeFondoSolidaridad());
        } else {

            $this->fondoSolidaridad = 0;
        }

        return $this;
    }
    
    public function calculoIBCRetencionFuente()
    {
        $salario_final = ($this->salarioFuncionario-($this->salud + $this->pension + $this->fondoSolidaridad + $this->fondoSubsistencia));
        $deduccion = (($this->salarioFuncionario-($this->salud + $this->pension + $this->fondoSolidaridad + $this->fondoSubsistencia))*25/100);
        
        $saldo =  $salario_final - $deduccion;
        
        $saldos_uvt = $saldo/$this->uvt;
        //dd($saldos_uvt);
        
        if ($saldos_uvt > 95 && $saldos_uvt <= 150) {
            $this->retencionFuente = round((($saldos_uvt-95)*$this->uvt) * 19 / 100);
        } elseif ($saldos_uvt > 150 && $saldos_uvt <= 360) {
            $this->retencionFuente = round((($saldos_uvt-150)*$this->uvt)  * 28 / 100) + (10*$this->uvt);
        } elseif ($saldos_uvt > 360 && $saldos_uvt <= 640) {
            $this->retencionFuente = round((($saldos_uvt-360)*$this->uvt)  * 33 / 100) + (69*$this->uvt);
        } elseif ($saldos_uvt > 640 && $saldos_uvt <= 945) {
            $this->retencionFuente = round((($saldos_uvt-640)*$this->uvt)  * 35 / 100)  + (162*$this->uvt);
        } elseif ($saldos_uvt > 945 && $saldos_uvt <= 2300) {
            $this->retencionFuente = round((($saldos_uvt-945)*$this->uvt)  * 37 / 100) + (268*$this->uvt);
        } elseif ($saldos_uvt > 2300) {
            $this->retencionFuente = round((($saldos_uvt-2300)*$this->uvt)  * 39 / 100) + (770*$this->uvt);
        } else {
            $this->retencionFuente = 0;
        }

        return $this;
    }

    public function getFondoSolidaridad()
    {
        return $this->fondoSolidaridad;
    }

    public function calculoIBCFondoSubsistencia()
    {
        if ($this->salarioFuncionario >= $this->salarioSMLV * 16 && $this->salarioFuncionario <= $this->salarioSMLV * 17) {

            $this->fondoSubsistencia = round($this->ibcSeguridad * $this->porcentajesFondoSubsistencia()['fondo_subsistencia_02']);
        } elseif ($this->salarioFuncionario > $this->salarioSMLV * 17 && $this->salarioFuncionario <= $this->salarioSMLV * 18) {

            $this->fondoSubsistencia = round($this->ibcSeguridad * $this->porcentajesFondoSubsistencia()['fondo_subsistencia_04']);
        } elseif ($this->salarioFuncionario > $this->salarioSMLV * 18 && $this->salarioFuncionario <= $this->salarioSMLV * 19) {

            $this->fondoSubsistencia = round($this->ibcSeguridad * $this->porcentajesFondoSubsistencia()['fondo_subsistencia_06']);
        } elseif ($this->salarioFuncionario > $this->salarioSMLV * 19 && $this->salarioFuncionario <= $this->salarioSMLV * 20) {

            $this->fondoSubsistencia = round($this->ibcSeguridad * $this->porcentajesFondoSubsistencia()['fondo_subsistencia_08']);
        } elseif ($this->salarioFuncionario > $this->salarioSMLV * 20) {

            $this->fondoSubsistencia = round($this->ibcSeguridad * $this->porcentajesFondoSubsistencia()['fondo_subsistencia_1']);
        }
         return $this;
    }

    public function getFondoSubsistencia()
    {
        return $this->fondoSubsistencia;
    }
    
    public function getRetencionFuente()
    {
        return $this->retencionFuente;
    }

    public function crearColeccion()
    {
        return new Collection([
            'retenciones' =>  $this->getRetenciones(),
            'total_retenciones' => new Collection([
                'Salud' => $this->getSalud(),
                'Pensión' => $this->getPension(),
                'Fondo de solidaridad' => $this->getFondoSolidaridad(),
                'Fondo de subsistencia' => $this->getFondoSubsistencia(),
                'Retencion en la Fuente' => $this->getRetencionFuente()
            ]),
            'porcentajes' => new Collection([
                'Salud' => $this->porcentajeSalud(),
                'Pensión' => $this->porcentajePension(),
                'Fondo de solidaridad' => $this->porcentajeFondoSolidaridad(),
                'Retencion en la Fuente' => 0,
            ]),
            'IBC_seguridad' =>  $this->getIbcSeguridad(),
            'valor_total' => $this->getIBCRetenciones()
        ]);
    }
}
