<?php

namespace App\Traits;

use App\Clases\stdObject;
use App\Models\Festivo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait CalculateRotativoExtras
{
    public function getDataExtra($argumentos)
    {

        $HorasExtrasDiurnas = 0;
        $HorasExtrasNocturnas = 0;
        $horasExtrasDominicalesFestivasDiurnas = 0;
        $horasExtrasDominicalesFestivasNocturnas = 0;
        $HorasExtrasNocturnasDominicales = 0;
        $HorasExtrasDiurnasDominicales = 0;


        // Obtengo El tiempo que sera extra

        $this->tiempoParaExtras =  abs($argumentos->tiempoAsignado  - $argumentos->tiempoLaborado);
        if ($this->tiempoParaExtras > 0) {

            [$HorasExtrasDiurnas,  $HorasExtrasNocturnas, $HorasExtrasDiurnasDominicales, $HorasExtrasNocturnasDominicales] =  $this->calculateExtras($argumentos,  $this->tiempoParaExtras);
        }

        $extraObject = new stdObject();
        $extraObject->HorasExtrasDiurnas = $HorasExtrasDiurnas;
        $extraObject->HorasExtrasNocturnas = $HorasExtrasNocturnas;
        $extraObject->horasExtrasDominicalesFestivasDiurnas = $horasExtrasDominicalesFestivasDiurnas;
        $extraObject->horasExtrasDominicalesFestivasNocturnas = $horasExtrasDominicalesFestivasNocturnas;
        $extraObject->HorasExtrasNocturnasDominicales = $HorasExtrasNocturnasDominicales;
        $extraObject->HorasExtrasDiurnasDominicales = $HorasExtrasDiurnasDominicales;
        return  $extraObject;
    }

    public function calculateExtras($argumentos,  $tiempoParaExtras)
    {
        $HorasExtrasNocturnas = 0;
        $HorasExtrasDiurnas = 0;
        $HorasExtrasNocturnas = 0;
        $HorasExtrasDiurnasDominicales = 0;
        $HorasExtrasNocturnasDominicales = 0;

        //  Compruebo si Solo se le pagan horas nopturnas
        if ($argumentos->FinTurnoReal->hour > 21 ||  ($argumentos->FinTurnoReal->hour > 00 &&  $argumentos->FinTurnoReal->hour <= 06)) {
            if (Carbon::parse($argumentos->FinTurnoReal)->englishDayOfWeek == 'Sunday') {
                $HorasExtrasNocturnasDominicales += $tiempoParaExtras;
                $this->setTiempoExtra($tiempoParaExtras);
            } else {
                $HorasExtrasNocturnas += $tiempoParaExtras;
                $this->setTiempoExtra($tiempoParaExtras);
            }
        }

        // Compruebo si Solo se le pagan horas extras diurnas
        if ($argumentos->FinTurnoReal->hour <= 21 && $argumentos->FinTurnoReal->hour >  06) {

            // verifico que no me tome el siguiente dia si el turno temina  a las 00
            if ($argumentos->FinTurnoReal->diffInMinutes($argumentos->fechaSalida->format('Y-m-d')  . '21:00:00') / 60 >= 24) {
                $tiempoParaInicioNocturno = $argumentos->FinTurnoReal->diffInMinutes($argumentos->fechaSalida->subDay()->format('Y-m-d')  . '21:00:00');
            } else {
                $tiempoParaInicioNocturno = $argumentos->FinTurnoReal->diffInMinutes($argumentos->fechaSalida->format('Y-m-d')  . '21:00:00');
            }

            
            // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
            if ($tiempoParaExtras - $argumentos->FinTurnoReal->diffInMinutes($argumentos->fechaSalida->format('Y-m-d')  . '21:00:00') <= 0) {
                // Asigno todo el tiempo Extra como diurno
                $HorasExtrasDiurnas = $tiempoParaExtras;
                $argumentos->FinTurnoReal->addMinutes($HorasExtrasDiurnas);
                $this->setTiempoExtra($HorasExtrasDiurnas);
            } else {


                // Asigno parte del tiempo a extras diurnas
                if (Carbon::parse($argumentos->FinTurnoReal)->englishDayOfWeek == 'Sunday' ||  in_array($argumentos->FinTurnoReal->format('Y-m-d'), $this->getFestivos())) {
                    $HorasExtrasDiurnasDominicales = $tiempoParaInicioNocturno;
                    $argumentos->FinTurnoReal->addMinutes($HorasExtrasDiurnasDominicales);
                    $tiempoParaExtras = $tiempoParaExtras - $HorasExtrasDiurnasDominicales;
                } else {
                    $HorasExtrasDiurnas = $tiempoParaInicioNocturno;
                    $argumentos->FinTurnoReal->addMinutes($HorasExtrasDiurnas);
                    // Obtengo el resto de horas que sumaran como nocturnas
                    $tiempoParaExtras = $tiempoParaExtras - $HorasExtrasDiurnas;
                }

                if ($argumentos->asistencia->format('Y-m-d') != $argumentos->fechaSalida->format('Y-m-d')) {
                    $tiempoParaInicioSiguienteDia = $argumentos->FinTurnoReal->diffInMinutes(Carbon::parse($argumentos->fechaSalida->format('Y-m-d') . '00:00:00'));
                } else {
                    $tiempoParaInicioSiguienteDia = $argumentos->FinTurnoReal->diffInMinutes(Carbon::parse($argumentos->fechaSalida->format('Y-m-d') . '00:00:00')->addDay());
                }

                if ($tiempoParaExtras - $tiempoParaInicioSiguienteDia < 0) {

                    if (Carbon::parse($argumentos->FinTurnoReal)->englishDayOfWeek == 'Sunday' ||  in_array($argumentos->FinTurnoReal->format('Y-m-d'), $this->getFestivos())) {
                        $HorasExtrasDiurnasDominicales += $tiempoParaExtras;
                        $argumentos->FinTurnoReal->addMinutes($HorasExtrasDiurnasDominicales);
                        $this->setTiempoExtra($tiempoParaExtras);
                    } else {
                        $HorasExtrasDiurnas += $tiempoParaExtras;
                        $argumentos->FinTurnoReal->addMinutes($HorasExtrasDiurnas);
                        $this->setTiempoExtra($tiempoParaExtras);
                    }
                } else {

                    // Asigno parte del tiempo extra nocturno el mismo dia
                    $HorasExtrasNocturnas += $tiempoParaInicioSiguienteDia;
                    $argumentos->FinTurnoReal->addMinutes($HorasExtrasNocturnas);
                    $tiempoParaExtras = $tiempoParaExtras - $tiempoParaInicioSiguienteDia;
                    $tiempoParaInicioNuevoDiurno = $argumentos->FinTurnoReal->diffInMinutes(Carbon::parse($argumentos->fechaSalida->format('Y-m-d') . '06:00:00'));

                    if (Carbon::parse($argumentos->FinTurnoReal)->englishDayOfWeek == 'Sunday' ||  in_array($argumentos->FinTurnoReal->format('Y-m-d'), $this->getFestivos())) {

                        if ($tiempoParaExtras - $tiempoParaInicioNuevoDiurno < 0) {
                            // Asigno todo el tiempo extra nocturno siguiente dia
                            $HorasExtrasNocturnasDominicales = $tiempoParaExtras;
                            $argumentos->FinTurnoReal->addMinutes($HorasExtrasNocturnasDominicales);
                            $this->setTiempoExtra($tiempoParaExtras);
                        } else {
                            // Asigno parte del tiempo extra nocturno siguiente dia
                            $HorasExtrasNocturnasDominicales = $tiempoParaInicioSiguienteDia;
                            $argumentos->FinTurnoReal->addMinutes($HorasExtrasNocturnasDominicales);
                            $tiempoParaExtras = $tiempoParaExtras - $tiempoParaInicioSiguienteDia;

                            //Horas Diurnas Siguiente Dia
                            $tiempoParaInicioNocturno = $argumentos->FinTurnoReal->diffInMinutes($argumentos->fechaSalida->format('Y-m-d') . '21:00:00');
                            // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                            if ($tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                                // Asigno todo el tiempo Extra como diurno
                                $HorasExtrasDiurnasDominicales =  $argumentos->HorasExtrasDiurnasDominicales + $tiempoParaExtras;
                                $argumentos->FinTurnoReal->addMinutes($HorasExtrasDiurnasDominicales);
                                $this->setTiempoExtra($tiempoParaExtras);
                            }
                        }
                    }
                    if ($tiempoParaExtras - $tiempoParaInicioNuevoDiurno < 0) {
                        // Asigno todo el tiempo extra nocturno siguiente dia
                        $HorasExtrasNocturnas += $tiempoParaExtras;
                        $argumentos->FinTurnoReal->addMinutes($HorasExtrasNocturnas);
                        $this->setTiempoExtra($tiempoParaExtras);
                    } else {
                        // Asigno parte del tiempo extra nocturno siguiente dia
                        $HorasExtrasNocturnas = $tiempoParaInicioSiguienteDia;
                        $argumentos->FinTurnoReal->addMinutes($HorasExtrasNocturnas);
                        $tiempoParaExtras = $tiempoParaExtras - $tiempoParaInicioSiguienteDia;
                        //Horas Diurnas Siguiente Dia
                        $tiempoParaInicioNocturno = $argumentos->FinTurnoReal->diffInMinutes($argumentos->fechaSalida->format('Y-m-d') . '21:00:00', false);
                        // Compruebo si sumandole lo que le falta para cotizar nocturno se le acaba el tiempo que gano extra
                        if ($tiempoParaExtras - $tiempoParaInicioNocturno < 0) {
                            // Asigno todo el tiempo Extra como diurno
                            $HorasExtrasDiurnas =  $HorasExtrasDiurnas + $tiempoParaExtras;
                            $argumentos->FinTurnoReal->addMinutes($HorasExtrasDiurnas);
                            $this->setTiempoExtra($tiempoParaExtras);
                        }
                    }
                }
            }
        }

        return [
            $HorasExtrasDiurnas,
            $HorasExtrasNocturnas,
            $HorasExtrasDiurnasDominicales,
            $HorasExtrasNocturnasDominicales
        ];
    }
}
