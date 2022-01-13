<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Support\Collection;


class CalculoSeguridad implements Coleccion
{
    use Porcentaje;

    protected $ibcSeguridad;
    protected $salarioFuncionario;
    protected $salarioBaseFuncionario;
    protected $extrasReportadas;
    protected $ingresosAdicionales;
    protected $vacacionesDisfrutadas = 0;
    protected $pension;
    protected $salud;
    protected $riesgos;
    protected $sena;
    protected $icbf;
    protected $cajaCompensacion;
    protected $ibcRiesgos;
    protected $ibcParafiscales;
    protected $totalSeguridadSocial;
    protected $totalParafiscales;
    protected $total;


    public function __construct($ibcSeguridad, $salarioFuncionario, $salarioBaseFuncionario, $extrasReportadas, $ingresosAdicionales, $vacacionesDisfrutadas)
    {
        $this->ibcSeguridad = $ibcSeguridad;
        $this->salarioFuncionario = $salarioFuncionario;
        $this->salarioBaseFuncionario = $salarioBaseFuncionario;
        $this->extrasReportadas = $extrasReportadas;
        $this->ingresosAdicionales = $ingresosAdicionales;
        $this->vacacionesDisfrutadas = $vacacionesDisfrutadas;
    }

    public function getSalarioFuncionario()
    {
        return $this->salarioFuncionario;
    }

    public function getExtrasReportadas()
    {
        return $this->extrasReportadas;
    }

    public function getIngresosAdicionales()
    {
        return $this->ingresosAdicionales;
    }

    public function getVacacionesDisfrutadas()
    {
        return $this->vacacionesDisfrutadas;
    }

    public function getIbcSeguridad()
    {
        return $this->ibcSeguridad;
    }

    public function calcularPension()
    {
        $this->pension = round($this->ibcSeguridad * $this->porcentajePension());
        return $this;
    }

    public function getPension()
    {
        return $this->pension;
    }

    public function calcularSalud($aplicaLey, $smmlv)
    {
        if ($aplicaLey && $this->salarioBaseFuncionario < ($smmlv * 10)) {
            $this->salud = 0;
        } else {
            $this->salud = round($this->ibcSeguridad * $this->porcentajeSalud());
        }
        return $this;
    }

    public function getSalud()
    {
        return $this->salud;
    }

    public function calcularRiesgos($funcionario)
    {
        $this->riesgos =  round($this->ibcRiesgos * $this->porcentajeRiesgosArl($funcionario));
      
    }

    public function getRiesgos()
    {
        return $this->riesgos;
    }

    public function calcularSena($aplicaley, $smmlv)
    {
        if ($aplicaley && $this->salarioBaseFuncionario < ($smmlv * 10)) {
            $this->sena = 0;
        } else {
            $this->sena = round($this->ibcParafiscales * $this->porcentajeSena());
        }
        return $this;
    }

    public function getSena()
    {
        return $this->sena;
    }

    public function calcularIcbf($aplicaLey, $smmlv)
    {
        if ($aplicaLey && $this->salarioBaseFuncionario < ($smmlv * 10)) {
            $this->icbf = 0;
        } else {
            $this->icbf = round($this->ibcParafiscales * $this->porcentajeIcbf());
        }
        return $this;
    }

    public function getIcbf()
    {
        return $this->icbf;
    }

    public function calcularCajaCompensacion()
    {
        $this->cajaCompensacion  = round($this->ibcParafiscales * $this->porcentajeCajaCompensacion());
    }

    public function getCajaCompensacion()
    {
        return $this->cajaCompensacion;
    }

    public function calcularIbcRiesgos($smmlv)
    {
        $this->ibcRiesgos = $this->salarioFuncionario + $this->extrasReportadas + $this->ingresosAdicionales;
        if ($this->ibcRiesgos < $smmlv) {
            $this->ibcRiesgos = $smmlv;
        }
        return $this;
    }

    public function getIbcRiesgos()
    {
        return $this->ibcRiesgos;
    }

    public function calcularIbcParafiscales()
    {
        $this->ibcParafiscales = $this->salarioFuncionario + $this->extrasReportadas + $this->ingresosAdicionales + $this->vacacionesDisfrutadas;
    }

    public function getIbcParafiscales()
    {
        return $this->ibcParafiscales;
    }

    public function calcularTotalSeguridadSocial()
    {
        $this->totalSeguridadSocial = $this->pension + $this->salud + $this->riesgos;
        return $this;
    }

    public function getTotalSeguridadSocial()
    {
        return $this->totalSeguridadSocial;
    }

    public function calcularTotalParafiscales()
    {
        $this->totalParafiscales = $this->sena + $this->icbf + $this->cajaCompensacion;
    }

    public function getTotalParafiscales()
    {
        return $this->totalParafiscales;
    }

    public function calcularTotal()
    {
        $this->total = $this->totalSeguridadSocial + $this->totalParafiscales;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function crearColeccion()
    {

        return new Collection([
            'ibc_riesgos' => new Collection([
                'Salario' => $this->getSalarioFuncionario(),
                'Horas extras' => $this->getExtrasReportadas(),
                'Ingresos' => $this->getIngresosAdicionales(),
                'IBC Riesgos' => $this->getIbcRiesgos(),
            ]),
            'ibc_parafiscales' => new Collection([
                'Valor' => $this->getSalarioFuncionario(),
                'Horas extras' => $this->getExtrasReportadas(),
                'Vacaciones' => $this->getVacacionesDisfrutadas(),
                'Ingresos' => $this->getIngresosAdicionales(),
                'IBC Parafiscales' => $this->getIbcParafiscales(),
            ]),
            'seguridad_social' => new Collection([
                'Salud' => $this->getSalud(),
                'Pensión' => $this->getPension(),
                'Riesgos' => $this->getRiesgos(),
            ]),
            'parafiscales' => new Collection([
                'Sena' => $this->getSena(),
                'Icbf' => $this->getIcbf(),
                'Caja de compensación' => $this->getCajaCompensacion(),
            ]),
            'valor_total' => $this->getTotal(),
            'valor_total_seguridad' => $this->getTotalSeguridadSocial(),
            'valor_total_parafiscales' => $this->getTotalParafiscales(),

        ]);
    }
}
