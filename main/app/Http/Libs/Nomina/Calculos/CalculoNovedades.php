<?php

namespace App\Http\Libs\Nomina\Calculos;

use App\Clases\PorcentajeInstance;
use App\Clases\stdObject;
use App\Models\ContableLicenciaIncapacidad;
use App\Models\EmpresaConfiguracion;
use App\Models\PayConfigurationCompany;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Clase para el cálculo de novedades en un determinado periodo
 */
class CalculoNovedades implements Coleccion
{
    private $dias = 0;
    private $salarioPromedio;
    private $novedadesRegistradas = [];
    private $totalesNovedad = [];
    private $valorTotal;
    private $inicioPeriodo;
    private $finPeriodo;
    private const PERIODO_CONTABLE = 30; // Dias
    private const INCAPACIDADBYDEFAULT = 'Incapacidad general'; // Dias
    private const PERCENTBYDEFAULF = 3; // Dias


    /**
     * Constructor
     *
     * @param int $salarioPromedio
     * @param string $inicioPeriodo
     * @param string $finPeriodo
     */
    public function __construct($salarioPromedio, $inicioPeriodo, $finPeriodo)
    {
        $this->salarioPromedio = $salarioPromedio;
        $this->inicioPeriodo = new Carbon($inicioPeriodo);
        $this->finPeriodo = new Carbon($finPeriodo);
    }

    /**
     * Obtener total de días de novedades en el periodo, ya sean vacaciones, licencias, etc.
     * Teniendo en cuenta esto para restarlo a los días trabajados
     *
     * @return int
     */
    public function getDias()
    {
        return $this->dias;
    }

    /**
     * Retorna el salario promedio de un funcionario, esto viene de la BD
     *
     * @return int
     */
    public function getSalarioPromedio()
    {
        return $this->salarioPromedio;
    }

    /**
     * Registra una novedad en el container (Array) por clave - valor
     *
     * @param String $llave
     * @param int $valor
     * @return void
     */
    public function registrarNovedad($llave, $valor, $novedad)
    {
        // $this->novedadesRegistradas[$llave] = $valor;
        $this->novedadesRegistradas[$llave] = ['value' => $valor, 'sum' => $novedad->sum];
    }

    /**
     * Retorna el container con las novedades registradas
     *
     * @return Array
     */
    public function getNovedades()
    {
        return $this->novedadesRegistradas;
    }


    /**
     * Getter totales novedad
     *
     * @return Array
     */
    public function getTotalesNovedad()
    {
        return $this->totalesNovedad;
    }

    /**
     * Getter valor total todas las novedades
     *
     * @return int
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Si exsiten vacaciones, entonces registrarlas con el método indicado
     *
     * @param Collection $vacaciones
     * @return void
     */
    public function existenVacaciones(Collection $vacaciones)
    {
        if ($vacaciones->isNotEmpty()) {
            $this->registroMasivoNovedades($vacaciones);
        }
    }

    /**
     * Registrar masivamente novedades, si ya existen en el container, simplemente se aumentan los días correspondientes de esa novedad, válido únicamente para vacaciones
     *
     * @param Collection $novedades
     * @return void
     */
    private function registroMasivoNovedades(Collection $novedades)
    {
        //Se utiliza en caso de que el concepto de la novedad ya esté en el array de novedadesRegistradas, únicamente para las vacaciones actualmente
        $diferenciaTemporal = 0;
        $novedades->each(function ($novedad) use (&$diferenciaTemporal) {

            $fechaInicio = new Carbon($novedad->date_start);
            $fechaFin = new Carbon($novedad->date_end);
            $diferencia = $fechaFin->diffInDays($fechaInicio) + 1;

            if (collect($this->novedadesRegistradas)->has($novedad->disability_leave->concept)) {
                $diferenciaTemporal = $this->novedadesRegistradas[$novedad->disability_leave->concept];
                $diferencia += $diferenciaTemporal;
            }

            $this->registrarNovedad($novedad->disability_leave->concept, $diferencia, $novedad);
            $this->dias += $diferencia - $diferenciaTemporal;
        });
    
    }

    public function custoRegisterNovedad($novedad)
    {
        // $incapacidad =  ContableLicenciaIncapacidad::findOrfail($novedad->contable_licencia_incapacidad_id);

        // if ($incapacidad->suma) {

        // }
        // array_push();

        // dd($novedad);
    }



    /**
     * Comprobar que las fechas de los periodos coincidan con las de las novedades
     *
     * @param String $fechaInicio
     * @param String $fechaFin
     * @return Boolean
     */
    private function comprobarCondiciones($fechaInicio, $fechaFin)
    {
        //Que la fecha del inicio de periodo de pago sea mayor/igual a la fecha de inicio de la novedad, o si es menor, que estén ambas en el mismo mes, por último que la fecha de inicio de periodo de pago sea menor/igual a la fecha final de la novedad (por si acaso....)
        $condicionUno = $this->inicioPeriodo->greaterThanOrEqualTo($fechaInicio) || ($this->inicioPeriodo->lessThan($fechaInicio) && $this->inicioPeriodo->isSameMonth($fechaInicio)) && $this->inicioPeriodo->lessThanOrEqualTo($fechaFin);

        //Que la fecha del fin de periodo de pago sea mayor/igual a la fecha de inicio de la novedad (por si acaso..), y que la fecha del fin de periodo de pago sea menor/igual a la fecha final de la novedad, o si es mayor, que estén ambas en el mismo mes.
        $condicionDos = $this->finPeriodo->greaterThanOrEqualTo($fechaInicio) &&
            $this->finPeriodo->lessThanOrEqualTo($fechaFin) || ($this->finPeriodo->greaterThan($fechaFin) && $this->finPeriodo->isSameMonth($fechaFin));

        if ($condicionUno && $condicionDos) {
            return true;
        }
        return false;
    }

    /**
     * Comprobar si existen las novedades y realizar operaciones (leer internamente el método)
     *
     * @param Collection $novedades
     * @return void
     */
    public function existenNovedades(Collection $novedades)
    {
        if ($novedades->isNotEmpty()) {
            //Se usa en caso de que el concepto de la novedad ya esté en el array de novedadesRegistradas
            $diferenciaTemporal = 0;
            $novedades->each(function ($novedad) use (&$diferenciaTemporal) {

                $fechaInicio = new Carbon($novedad->date_start);
                $fechaFin = new Carbon($novedad->date_end);

                if ($this->comprobarCondiciones($fechaInicio, $fechaFin)) {
                    $dias = $this->inicioPeriodo->diffInDays($this->finPeriodo) + 1;
                    //Febrero
                    $dias = (($dias > 15 && $dias < 30) || $dias > 30) ? 30 : $dias;
                    
                    
                    //Si la novedad está en el mismo mes, descartar automáticamente demás opciones y asignar los dias iguales a la diferencia
                    if ($fechaInicio->isSameMonth($fechaFin)) {
                        $diferencia = $fechaFin->diffInDays($fechaInicio) + 1;
                        $dias = $diferencia;
                    } else if ($this->inicioPeriodo->lessThan($fechaInicio) && $this->inicioPeriodo->isSameMonth($fechaInicio)) {
                        $diferencia = $fechaInicio->diffInDays($this->inicioPeriodo);
                        $dias -= $diferencia;
                    } else if ($this->finPeriodo->greaterThan($fechaFin) && $this->finPeriodo->isSameMonth($fechaFin)) {
                        $diferencia = $fechaFin->diffInDays($this->inicioPeriodo) + 1;
                        $dias = $diferencia;
                    }
                    //Si ya existe el concepto de la novedad en el array novedadesRegistradas, entonces solo se suma a la que existe los días de la entrante, ej: suspensión => 2 días, suspensión => 1 día igual a suspensión => 3 días
                 
                    if (collect($this->novedadesRegistradas)->has($novedad->disability_leave->concept)) {
                        $diferenciaTemporal = $this->novedadesRegistradas[$novedad->disability_leave->concept]['value'];
                       
                        $dias += $diferenciaTemporal;
                    }

                    // $this->custoRegisterNovedad($novedad);
                    // $this->registrarNovedad($novedad->novedad->concepto, $diferencia, $novedad);

                    $this->registrarNovedad($novedad->disability_leave->concept, $dias, $novedad);
                    $this->dias += $dias - $diferenciaTemporal;
                }
            });

        }
    }

    /**
     * Calcular el valor total de cada novedad
     *
     * @return int
     */
    public function totalizarNovedad()
    {
        $configuracion = PayConfigurationCompany::with('percentage:id,value')->exclude(['created_at', 'updated_at'])->first();
        


        foreach ($this->getNovedades() as $novedad => $item) {
            $objeParams = new stdObject();
            $objeParams->salario = $this->salarioPromedio;
            $objeParams->dias = $item['value'];
            $objeParams->periodo = self::PERIODO_CONTABLE;
            $objeParams->suma = $item['sum'];

            if (self::INCAPACIDADBYDEFAULT == $novedad) {
                $objeParams->porcentaje = $configuracion->porcentaje->id;
            } else {
                $objeParams->porcentaje = self::PERCENTBYDEFAULF;
            }

            $porcentajeInstance =  new PorcentajeInstance($objeParams);
            $instance =  $porcentajeInstance->getSalario();
            $this->totalesNovedad[$novedad]  = $instance->calculateIngresoDeIncapacidad();
        }

        return $this;
    }

    /**
     * Calcular el valor total de todas las novedades
     *
     * @return int
     */
    public function calcularValorTotal()
    {
        $this->valorTotal =  collect($this->getTotalesNovedad())->values()->sum();
    }

    /** 
     * Aplicar el contract de la interfaz, crear la colección 
     *  
     * @return Illuminate\Support\Collection
     * 
     * */
    public function crearColeccion()
    {

        $res = [];


        foreach ($this->getNovedades() as $k => $item) {
            $res[$k] = $item['value'];
        }

        return new Collection([
            'total_dias' => $this->getDias(),
            'novedades' => $res,
            'novedades_totales' => $this->getTotalesNovedad(),
            'valor_total' => $this->getValorTotal()
        ]);
    }
}
