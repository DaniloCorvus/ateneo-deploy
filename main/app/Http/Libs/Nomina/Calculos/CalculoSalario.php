<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


class CalculoSalario implements Coleccion
{
    private $salarioBase;
    private $salarioPromedio;
    private $salarioDiasTrabajados;
    private $diasTrabajados;
    private $diasNovedades;
    private $diasPeriodo;
    private $subsidioTransporte;
    public  $valorSubsidioTransporte;
    private $fechaInicio;
    private $fechaFin;
    private $diasNoTrabajados = 0;

    public function __construct($salarioBase, $diasNovedades, $fechaInicio, $fechaFin)
    {
        $this->salarioBase = $salarioBase;
        $this->salarioPromedio = $salarioBase;
        $this->diasNovedades = $diasNovedades;
        $this->fechaInicio = new Carbon($fechaInicio);
        $this->fechaFin = new Carbon($fechaFin);

        $this->diasPeriodo = $this->fechaFin->diffInDays($this->fechaInicio) + 1;

        //var_dump($this->diasPeriodo);
        $this->diasPeriodo = (($this->diasPeriodo > 30 ) ? 30 : (($this->diasPeriodo>16 && $this->diasPeriodo<30) ? 30 : ((($this->diasPeriodo>15&&$this->diasPeriodo<28)||$this->diasPeriodo<14)  ? 15 : $this->diasPeriodo))) ;
    }

    public function verificarFechasContrato($fechaInicio, $fechaFin)
    {
        $inicio = $fechaInicio != null ?  Carbon::parse($fechaInicio) : $this->fechaInicio;
        $fin = $fechaFin != null ?  Carbon::parse($fechaFin) : $this->fechaFin;
        if ($inicio->greaterThan($this->fechaInicio)) {
            
            if (Carbon::now()->daysInMonth > 30) {
                $this->diasNoTrabajados = $inicio->diffInDays($this->fechaInicio);
            } else {
                $this->diasNoTrabajados = $inicio->diffInDays($this->fechaInicio);
            }
        }
        
      
        if ($fin->lessThan($this->fechaFin)) {
            
            //echo "entro al else<br>";
            
            
            if (Carbon::now()->daysInMonth > 30) {
                //echo "entro a >30<br>";
                //echo $this->fechaFin->diffInDays($fin)."<br>==========";
                $this->diasNoTrabajados += $this->fechaFin->diffInDays($fin);
                if($this->fechaFin->format('j')>15){
                    //$this->diasNoTrabajados-=1;
                }
            } else {
              
                $this->diasNoTrabajados += $this->fechaFin->diffInDays($fin) ;
            }
        }
    }

    public function setDiasNovedades($diasNovedades)
    {
        $this->diasNovedades = $diasNovedades;
    }

    public function getDiasNovedades()
    {
        return $this->diasNovedades;
    }

    public function getDiasPeriodo()
    {
        return $this->diasPeriodo;
    }

    public function getSubsidioTransporte()
    {
        return $this->subsidioTransporte;
    }

    public function calcularDiasTrabajados()
    {
        $this->diasTrabajados = $this->diasPeriodo - $this->diasNovedades;
        if ($this->diasNoTrabajados > 0) {
            $this->diasTrabajados -= $this->diasNoTrabajados;
        }
        
        
        return $this;
    }

    public function calcularTransporteDiasTrabajados($aplica)
    {
        if (!$aplica) {
            $this->subsidioTransporte = 0;
        } else {
            $this->subsidioTransporte = round($this->valorSubsidioTransporte * $this->diasTrabajados / 30);
        }
    }

    public function calcularSalarioDiasTrabajados()
    {
        $this->salarioDiasTrabajados = round($this->salarioBase * $this->diasTrabajados / 30);
    }

    public function getDiasTrabajados()
    {
        return $this->diasTrabajados;
    }

    public function getDiasNoTrabajados()
    {
        return $this->diasNoTrabajados;
    }

    public function getSalarioDiasTrabajados()
    {
        return $this->salarioDiasTrabajados;
    }

    public function crearColeccion()
    {
        return new Collection([
            // 'dias_trabajados' =>0,
            // 'dias_periodo' =>'',
            // 'dias_no_trabajados' =>0,
            // 'salario' =>0,
            // 'auxilio_transporte' =>0,

            /* 'dias_trabajados' => $this->getDiasTrabajados(),
            'dias_periodo' => $this->getDiasPeriodo(),
            'dias_no_trabajados' => $this->getDiasNoTrabajados(),
            'salario' => $this->getSalarioDiasTrabajados(),
            'auxilio_transporte' => $this->getSubsidioTransporte(), */

            'worked_days' => $this->getDiasTrabajados(),
            'period_days' => $this->getDiasPeriodo(),
            'not_worked_days' => $this->getDiasNoTrabajados(),
            'salary' => $this->getSalarioDiasTrabajados(),
            'transportation_assistance' => $this->getSubsidioTransporte(),
        ]);
    }
}
