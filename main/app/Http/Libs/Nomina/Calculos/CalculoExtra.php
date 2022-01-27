<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CalculoExtra implements Coleccion
{
    protected $cantidadHoras = [];
    protected $horasReportadas = [];
    private $horasTotales = [];
    private $valorTotal;
    private $reporte;
    protected $salario;
    private $porcentajes = [];
    private $prefijos;


    /**
     * Settear los prefijos de la tabla, esto se necesitará para varias operaciones.
     * Settear el reporte de horas, como consulta sin retornar ya que se necesitará acceder a métodos del          queribuilder en algunos métodos de esta clase.
     * Settear el salario del funcionario, esto se necesitará para las operaciones de cálculo por las horas        extras trabajadas.
     *
     * @param array $prefijos
     * @param Builder $reporte
     * @param int $salario
     */
    public function __construct($prefijos, Builder $reporte, $salario)
    {
        $this->prefijos = $prefijos;
        $this->reporte = $reporte;
        $this->salario = $salario;
    }

    /**
     * Settear las horas reportadas del funcionario en el periodo
     *
     * @param array $horasReportadas
     * @return void
     */
    public function setHorasReportadas($horasReportadas)
    {

        $this->horasReportadas = $horasReportadas;
        
    }

    /**
     * Settear el valor total de extras y recargos a pagar al funcionario en el periodo correspondiente
     *
     * @param int $valorTotal
     * @return void
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    /**
     * Settear los porcentajes asociados a cada hora, ej: hed => 1.25, hen => 1.75, etc
     *
     * @param array $porcentajes
     * @return void
     */
    public function setPorcentajes($porcentajes)
    {
      
        $this->porcentajes = $porcentajes;
    }

    /**
     * Obtener los prefijos de la tabla
     *
     * @return array
     */
    public function getPrefijos()
    {
        return $this->prefijos;
    }

    /**
     * Obtener el reporte de extras general
     *
     * @return QuerBuilder
     */
    public function getReporte()
    {
        return $this->reporte;
    }

    /**
     * Obtener el salario del funcionario
     *
     * @return int
     */
    public function getSalario()
    {
        return $this->salario;
    }
    /**
     * Obtener las horas trabajadas con sus respectivos valores en pesos
     *
     * @return array
     */
    public function getHorasTotales()
    {
        return $this->horasTotales;
    }

    /**
     * Obtener la cantidad de horas 
     *
     * @return void
     */
    public function getCantidadHoras()
    {
        return $this->cantidadHoras;
    }
    /**
     * Obtener el valor total a pagar al funcionario 
     *
     * @return int
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Obtener la cantidad de horas trabajadas por cada hora o prefijo, ej: hed: 7, hen: 1
     *
     * @return void
     */
    public function getHorasReportadas()
    {
        return $this->horasReportadas;
    }

    /**
     * Obtener los porcentajes de cada hora
     *
     * @return array
     */
    public function getPorcentajes()
    {
        return $this->porcentajes;
    }

    /**
     * Calcular y asignar la cantidad de horas trabajadas
     *
     * @return void
     */
    public function calcularCantidadHoras()
    {
        foreach ($this->getPrefijos() as $indice) {
            $this->cantidadHoras[$indice] = $this->getReporte()->get([$indice])->sum([$indice]);
        }
       
    }

    /**
     * Calcular y asignar los valores a pagar por cada tipo de hora o prefijo
     *
     * @return void
     */
    public function calcularTotalHoras()
    {
        foreach ($this->getPrefijos() as $indice) {
            $this->horasTotales[$indice] = round($this->getHorasReportadas()[$indice] * $this->getPorcentajes()[$indice] * $this->salario / (30 * 8));
        }

      

 
    }
    /**
     * Calcular o sumar todo lo que se debe pagar al funcionario por el concepto de extras y recargos
     *
     * @return void
     */
    public function calcularValorTotalHoras()
    {
        
        $this->setValorTotal(collect($this->getHorasTotales())->values()->sum());
    }

    /**
     * Aplicar el contract de la interfaz, crear la colección
     *
     * @return Illuminate\Support\Collection
     */
    public function crearColeccion()
    {
        return new Collection([
            'horas_reportadas' => $this->getHorasReportadas(),
            'horas_extras_totales' => $this->getHorasTotales(),
            'valor_total' => $this->getValorTotal()
        ]);
    }
}
