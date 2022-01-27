<?php

namespace App\Traits;

use App\Clases\stdObject;
use App\Models\Festivo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait CalculateRotativoRecargos
{

    public function getDataRecargo($argumentos)
    {

        $horasRecargoDominicalNocturna = 0;
        $horasRecargoDominicalDiurno = 0;
        $horasRecargoFestivo = 0;
        $horasRecargoNocturna = 0;

        //Obtengo El tiempo que sera extra

        $this->tiempoParaExtras =  abs($argumentos->tiempoAsignado  - $argumentos->tiempoLaborado);
        if ($this->tiempoParaExtras > 0) {
            [$horasRecargoDominicalNocturna,  $horasRecargoNocturna, $horasRecargoDominicalDiurno] =  $this->calculateRecargos($argumentos);
        }

        $extraObject = new stdObject();
        $extraObject->horasRecargoDominicalNocturna = $horasRecargoDominicalNocturna;
        $extraObject->horasRecargoDominicalDiurno = $horasRecargoDominicalDiurno;
        $extraObject->horasRecargoFestivo = $horasRecargoFestivo;
        $extraObject->horasRecargoNocturna = $horasRecargoNocturna;
        return  $extraObject;
    }

    public function getDataRecargoSinExtras($argumentos)
    {

        $horasRecargoDominicalNocturna = 0;
        $horasRecargoDominicalDiurno = 0;
        $horasRecargoFestivo = 0;
        $horasRecargoNocturna = 0;

        //Obtengo El tiempo que sera extra
        [$horasRecargoDominicalNocturna, $horasRecargoNocturna, $horasRecargoDominicalDiurno] =  $this->calculateRecargos($argumentos);

        $extraObject = new stdObject();
        $extraObject->horasRecargoDominicalNocturna = $horasRecargoDominicalNocturna;
        $extraObject->horasRecargoDominicalDiurno = $horasRecargoDominicalDiurno;
        $extraObject->horasRecargoFestivo = $horasRecargoFestivo;
        $extraObject->horasRecargoNocturna = $horasRecargoNocturna;
        return  $extraObject;
    }

    /************************************************************************Recargos cuando se tiene horas extas  *********************************************************************************/
    public function calculateRecargos($info)
    {

        $horasRecargoDominicalNocturna = 0;
        $horasRecargoDominicalDiurno = 0;
        $horasRecargoFestivo = 0;
        $horasRecargoNocturna = 0;

        // asistencia

        if ($info->asistencia->hour <= 21) {
            if ($info->asistencia->copy()->addMinutes($info->tiempoAsignado)->hour >= 21) {
                //    ajustar si la hora de salida esta entre las 21 y las 00
            }
            //       Se verifica si la hora de salida normal del turno se encuentra entre las 00 y las 06 
            if ($info->asistencia->copy()->addMinutes($info->tiempoAsignado)->hour >= 00 &  $info->asistencia->copy()->addMinutes($info->tiempoAsignado)->hour <= 06) {

                //       Se verifica si la fecha de salida es un dia domingo
                if ($info->fechaSalida->englishDayOfWeek == 'Sunday' || in_array($info->fechaSalida->format('Y-m-d'), $this->getFestivos())) {

                    $horasRecargoDominicalNocturna +=
                        Carbon::parse($info->fechaSalida->format('Y-m-d') . '00:00:00')->diffInMinutes($info->asistencia->copy()->addMinutes($info->tiempoAsignado));
                }

                $horasRecargoNocturna +=
                    Carbon::parse($info->asistencia->format('Y-m-d') . '21:00:00')->diffInMinutes($info->asistencia->copy()->addMinutes($info->tiempoAsignado));
            }
            //Compruebo si la asistencia es un domingo para calcular festivas
            if ($info->asistencia->englishDayOfWeek == 'Sunday' || in_array($info->asistencia->format('Y-m-d'), $this->getFestivos())) {

                if ($info->asistencia->format('Y-m-d') == $info->fechaSalida->format('Y-m-d')) {

                    if ($info->fechaSalida->hour <= 21) {
                        $horasRecargoDominicalDiurno +=
                            $info->asistencia->diffInMinutes(Carbon::parse($info->fechaSalida));
                    }
                } else {

                    if ($info->fechaSalida->hour <= 06) {

                        $horasRecargoDominicalDiurno +=
                            $info->asistencia->diffInMinutes(Carbon::parse($info->asistencia->format('Y-m-d') . '21:00:00'));

                        $horasRecargoDominicalNocturna +=
                            Carbon::parse($info->asistencia->format('Y-m-d') . '21:00:00')->diffInMinutes($info->fechaSalida->format('Y-m-d') . '00:00:00');
                            
                    }
                }
            }
        }

        // compruebo si la asistencia es despues de las 21 
        if ($info->asistencia->hour > 21) {

            // compruebo si el tiempo asignado es hasta las 06 de la mañana o antes  
            if ($info->asistencia->copy()->addMinutes($info->tiempoAsignado)->hour <= 06) {

                // compruebo si el siguiente dia es un domingo  
                if ($info->fechaSalida->englishDayOfWeek == 'Sunday' || in_array($info->asistencia->format('Y-m-d'), $this->getFestivos())) {
                    $horasRecargoNocturna +=
                        Carbon::parse($info->asistencia)->diffInMinutes(Carbon::parse($info->fechaSalida->format('Y-m-d') . '00:00:00'));
                    $horasRecargoDominicalNocturna +=
                        Carbon::parse(Carbon::parse($info->fechaSalida->format('Y-m-d') . '00:00:00'))
                        ->diffInMinutes($info->asistencia->copy()->addMinutes($info->tiempoAsignado));
                } else {
                    $horasRecargoNocturna +=
                        Carbon::parse($info->asistencia)->diffInMinutes($info->asistencia->copy()->addMinutes($info->tiempoAsignado));
                }
            } else {
                if ($info->fechaSalida->englishDayOfWeek == 'Sunday' || in_array($info->asistencia->format('Y-m-d'), $this->getFestivos())) {
                    $horasRecargoNocturna +=
                        Carbon::parse($info->asistencia)->diffInMinutes(Carbon::parse($info->fechaSalida->format('Y-m-d') . '00:00:00'));
                    $horasRecargoDominicalNocturna +=
                        Carbon::parse(Carbon::parse($info->fechaSalida->format('Y-m-d') . '00:00:00'))
                        ->diffInMinutes(Carbon::parse($info->fechaSalida->format('Y-m-d') . '06:00:00'));
                } else {
                    $horasRecargoNocturna +=
                        Carbon::parse($info->asistencia)->diffInMinutes(Carbon::parse($info->fechaSalida->format('Y-m-d') . '06:00:00'));
                }
            }

            if ($info->asistencia->englishDayOfWeek == 'Sunday' || in_array($info->asistencia->format('Y-m-d'), $this->getFestivos())) {
                $horasRecargoDominicalNocturna +=
                    $info->asistencia
                    ->diffInMinutes(Carbon::parse($info->asistencia->format('Y-m-d') . '00:00:00')->addDay());
            }
        } else {
            if ($info->fechaSalida->hour > 00 && $info->fechaSalida->hour <= 06) {
            }
            if ($info->fechaSalida->hour == 00 || $info->fechaSalida->hour >= 21) {
                
                $horasRecargoNocturna +=
                    Carbon::parse($info->asistencia->format('Y-m-d') . '21:00:00')->diffInMinutes($info->fechaSalida);
            }
        }

        return [$horasRecargoDominicalNocturna, $horasRecargoNocturna, $horasRecargoDominicalDiurno];
    }
}
