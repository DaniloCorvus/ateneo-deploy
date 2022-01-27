<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


class CalculoProvisiones implements Coleccion
{
    protected $salarioFuncionario;
    protected $extrasReportadas;
    protected $novedades = [];
    protected $ingresos;
    protected $recargosNocturnos;
    protected $auxilioTransporte;
    protected $baseCesantias;
    protected $basePrima;
    protected $baseVacaciones;
    protected $diasPeriodo;
    protected $diasBaseVacaciones;
    protected $vacacionesPeriodo;
    protected $diasHabilesCompensados;
    protected $cesantias;
    protected $interesesCesantias;
    protected $prima;
    protected $vacaciones;
    protected $totalProvisiones;

    /** Array de excepciones para el cálculo de los días base de vacaciones, si en la tabla de contable_licencia_incapacidad se cambia alguno de estos conceptos se debe actualizar acá también. */
    const EXCEPCIONES = [
        'Licencia remunerada', 'Licencia no remunerada', 'Suspensión', 'Abandono del puesto de trabajo'
    ];
    /** El factor 4.167% corresponde a 15 días hábiles de descanso remunerado por cada año cumplido de labor (15     días / 360 días = 0.041666667 = 4.167 % ). Si por ley, el parámetro se afecta, se debe actualizar acá */
    const FACTOR_VACACIONES = 0.0416667;
    /** El factor  8.33% corresponde a 30 días de salario por cada año cumplido de labor ( 30 días / 360 días  = 0.0833 % ). Si por ley, el parámetro se afecta, se debe actualizar acá el nuevo valor*/
    const FACTOR_CESANTIAS_PRIMA = 0.0833333;
    /*** El factor 12% corresponde a los intereses de las cesantías */
    const FACTOR_INTERESES_CESANTIAS = 0.1200;


    /**
     * Constructor
     *
     * @param int $salarioFuncionario
     * @param int $extrasReportadas
     * @param Array $novedades
     * @param int $ingresos
     * @param int $recargosNocturnos
     * @param int $auxilioTransporte
     */
    public function __construct($salarioFuncionario, $extrasReportadas, $novedades, $ingresos, $recargosNocturnos, $auxilioTransporte)
    {
        $this->salarioFuncionario = $salarioFuncionario;
        $this->extrasReportadas = $extrasReportadas;
        $this->novedades = $novedades;
        $this->ingresos = $ingresos;
        $this->recargosNocturnos = $recargosNocturnos;
        $this->auxilioTransporte = $auxilioTransporte;
    }

    public function getSalarioFuncionario()
    {
        return $this->salarioFuncionario;
    }

    public function getExtrasReportadas()
    {
        return $this->extrasReportadas;
    }

    public function getNovedades()
    {
        return $this->novedades;
    }

    public function getIngresos()
    {
        return $this->ingresos;
    }

    public function getRecargosNocturnos()
    {
        return $this->recargosNocturnos;
    }

    public function getAuxilioTransporte()
    {
        return $this->auxilioTransporte;
    }

    public function getBaseCesantias()
    {
        return $this->baseCesantias;
    }

    public function getBasePrima()
    {
        return $this->basePrima;
    }

    public function getBaseVacaciones()
    {
        return $this->baseVacaciones;
    }

    public function getDiasPeriodo()
    {
        return $this->diasPeriodo;
    }

    public function getDiasBaseVacaciones()
    {
        return $this->diasBaseVacaciones;
    }

    public function getVacacionesPeriodo()
    {
        return $this->vacacionesPeriodo;
    }

    public function getDiasHabilesCompensados()
    {
        return $this->diasHabilesCompensados;
    }

    public function getCesantias()
    {
        return $this->cesantias;
    }

    public function getInteresesCesantias()
    {
        return $this->interesesCesantias;
    }

    public function getPrima()
    {
        return $this->prima;
    }

    public function getVacaciones()
    {
        return $this->vacaciones;
    }

    public function getTotalProvisiones()
    {
        return $this->totalProvisiones;
    }

    /**
     * Calcular días del periodo de pago
     *
     * @param String $fechaInicio
     * @param String $fechaFin
     * @return void
     */
    public function calcularDiasPeriodo($fechaInicio, $fechaFin)
    {
        $this->diasPeriodo = Carbon::parse($fechaFin)->diffInDays(Carbon::parse($fechaInicio)) + 1;
        $this->diasPeriodo = ($this->diasPeriodo > 15 && $this->diasPeriodo < 30 || $this->diasPeriodo > 30) ? 30 : $this->diasPeriodo;
    }

    /**
     * Calcular el total de novedades
     *
     */
    private function sumaNovedades()
    {
        return collect($this->novedades)->values()->sum();
    }

    /**
     * Calcular base de cesantías y prima, método de encadenamiento
     *
     * @return CalculoProvisiones
     */
    public function calcularBase()
    {
        $this->baseCesantias = $this->basePrima = $this->salarioFuncionario + $this->extrasReportadas + $this->sumaNovedades() + $this->ingresos + $this->auxilioTransporte;
        return $this;
    }


    /**
     * Calcular la base para las vacaciones
     *
     * @return void
     */
    public function calcularBaseVacaciones()
    {
        $this->baseVacaciones = $this->salarioFuncionario + $this->sumaNovedades() +  $this->ingresos + $this->recargosNocturnos;
    }

    /**
     * Calcular los días base de las vacaciones, se deben excluir las demás días de novedades novedades, en caso de que hayan
     *
     * @param array $novedadesDias
     * @return void
     */
    public function calcularDiasBaseVac($novedadesDias = [])
    {
        $diasADescontar = 0;
        foreach ($novedadesDias as $novedad => $valor) {
            if (in_array($novedad, self::EXCEPCIONES)) {
                $diasADescontar += $valor;
            }
        }
        $this->diasBaseVacaciones = $this->diasPeriodo - $diasADescontar;
    }

    /**
     * Calcular los días hábiles (lunes a viernes) de vacaciones, esto para saber cuantos días de vacaciones acumula el funcionario
     *
     * @param Collection $vacaciones
     * @return void
     */
    public function calcularDiasHabiles(Collection $vacaciones)
    {
        $diasHabiles = 0;

        if ($vacaciones->isNotEmpty()) {
            $vacaciones->each(function ($vac) use (&$diasHabiles) {
                $inicio = new Carbon($vac->fecha_inicio);
                $fin = new Carbon($vac->fecha_fin);
                $inicioFecha = $inicio;

                while ($inicioFecha->lessThanOrEqualTo($fin)) {
                    $dia = $inicioFecha->dayOfWeekIso;
                    if ($dia > 0 && $dia < 6) {
                        $diasHabiles++;
                    }
                    $inicioFecha->addDay(1);
                }
            });
        }
        $this->diasHabilesCompensados = $diasHabiles;
    }

    /**
     * Calcular las vacaciones que se acumulan en el periodo, esto para saber cuantos días de vacaciones acumula el funcionario
     *
     * @return void
     */
    public function calcularVacacionesPeriodo()
    {
        $this->vacacionesPeriodo = number_format(($this->diasBaseVacaciones * self::FACTOR_VACACIONES) - $this->diasHabilesCompensados, 3, '.', '');
    }

    /**
     * Calcular valor de las cesantías en el periodo
     *
     */
    public function calcularCesantias()
    {
        $this->cesantias = $this->prima = round($this->baseCesantias * self::FACTOR_CESANTIAS_PRIMA);
        return $this;
    }

    /**
     * Calcular el valor de los intereses de las cesantías en el periodo
     *
     */
    public function calcularInteresesCesantias()
    {
        $this->interesesCesantias = round($this->cesantias * self::FACTOR_INTERESES_CESANTIAS);
        return $this;
    }

    /**
     * Calcular el valor en pesos de las provisiones de las vacaciones del periodo
     *
     * @return void
     */
    public function calcularVacaciones()
    {
        $this->vacaciones = round($this->baseVacaciones * self::FACTOR_VACACIONES);
    }


    /**
     * Calcular el valor total de provsiones de ese periodo
     *
     * @return void
     */
    public function calcularTotalProvisiones()
    {
        $this->totalProvisiones = $this->cesantias + $this->interesesCesantias + $this->prima + $this->vacaciones;
    }


    /**
     * Aplicar el contract de la interfaz, crear la colección
     *
     * @return Illuminate\Support\Collection
     */
    public function crearColeccion()
    {
        return new Collection([
            'base_cesantias' => new Collection([
                'Salario' => $this->getSalarioFuncionario(),
                'Horas extras' => $this->getExtrasReportadas(),
                'novedades' => $this->getNovedades(),
                'Ingresos' => $this->getIngresos(),
                'Auxilio de transporte' => $this->getAuxilioTransporte(),
                'Base cesantías' => $this->getBaseCesantias()
            ]),
            'base_prima' => new Collection([
                'Salario' => $this->getSalarioFuncionario(),
                'Horas extras' => $this->getExtrasReportadas(),
                'novedades' => $this->getNovedades(),
                'Ingresos' => $this->getIngresos(),
                'Auxilio de transporte' => $this->getAuxilioTransporte(),
                'Base prima' => $this->getBasePrima()
            ]),
            'base_vacaciones' => new Collection([
                'Salario' => $this->getSalarioFuncionario(),
                'novedades' => $this->getNovedades(),
                'Ingresos' => $this->getIngresos(),
                'Recargos nocturnos' => $this->getRecargosNocturnos(),
                'Base vacaciones' => $this->getBaseVacaciones()
            ]),
            'dias_vacaciones' => new Collection([
                'dias_base_vacaciones' => $this->getDiasBaseVacaciones(),
                'dias_habiles_vacaciones' => $this->getDiasHabilesCompensados(),
                'vacaciones_acumuladas_periodo' => $this->getVacacionesPeriodo(),
            ]),

            'resumen' => new Collection([
                'cesantias' => new Collection([
                    'base' => $this->getBaseCesantias(),
                    'concepto' => 'Cesantías',
                    'porcentaje' => self::FACTOR_CESANTIAS_PRIMA,
                    'valor' =>  $this->getCesantias(),
                ]),
                'intereses_cesantias' => new Collection([
                    'base' => $this->getCesantias(),
                    'concepto' => 'Intereses Cesantías',
                    'porcentaje' => self::FACTOR_INTERESES_CESANTIAS,
                    'valor' =>  $this->getInteresesCesantias(),
                ]),
                'prima' => new Collection([
                    'base' => $this->getBasePrima(),
                    'concepto' => 'Prima',
                    'porcentaje' => self::FACTOR_CESANTIAS_PRIMA,
                    'valor' =>  $this->getPrima(),
                ]),
                'vacaciones' => new Collection([
                    'base' => $this->getBaseVacaciones(),
                    'concepto' => 'Vacaciones',
                    'porcentaje' => self::FACTOR_VACACIONES,
                    'valor' =>  $this->getVacaciones(),
                ]),
            ]),
            'valor_total' => $this->getTotalProvisiones()

        ]);
    }
}
