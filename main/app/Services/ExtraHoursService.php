<?php

namespace App\Services;

use App\Clases\stdObject;
use App\Models\Company;
use App\Models\Person;
use App\Models\RotatingTurn;
use App\Services\TranslateService;
use App\Traits\CalculateRotativoExtras;
use App\Traits\CalculateRotativoRecargos;
use App\Traits\Festivos;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExtraHoursService
{
    use Festivos;
    use CalculateRotativoExtras;
    use CalculateRotativoRecargos;
    public  $tiempoParaExtras;


    /**Funciones de estadisticas */
    public function getLates($dates)
    {
        return  DB::table('late_arrivals as l')
            ->join('people as p', 'l.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                        join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->whereBetween(DB::raw('DATE(l.created_at)'), $dates)
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->selectRaw('count(*) as total')
            ->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(l.real_entry) - TIME_TO_SEC(l.entry))) AS time_diff_total')
            ->first();
    }

    public function getAllByDependecies($dates)
    {
        return DB::table('late_arrivals as l')
            ->join('people as p', 'l.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('positions as ps', 'ps.id', '=', 'w.position_id')
            ->join('dependencies as d', 'd.id', '=', 'ps.dependency_id')
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->whereBetween(DB::raw('DATE(l.created_at)'), $dates)
            ->selectRaw('count(*) as total, d.name')
            ->groupBy('d.id')
            ->get();
    }



    /**Arrivals */

    public function getPeople($id, $turn, $company_id, $dates)
    {
        $query = DB::table('people as p')
            ->join('work_contracts as w', function ($join) use ($dates) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                join people as u2 on u2.id = a2.person_id 
                join work_contract_types as wt on a2. work_contract_type_id = wt.id 
                WHERE date(a2.date_of_admission) <= "' . $dates[1] . '" 
                    and(
                        date(a2.date_end) >= "' . $dates[0] . '"  or  wt.conclude = 0
                    )
                group by u2.id
           
                )');
            })
            ->join('positions as ps', 'ps.id', '=', 'w.position_id')
            ->where('ps.dependency_id', $id)
            ->where('w.company_id', $company_id)
            ->where('w.turn_type', $turn)
            ->where('p.status', '!=', 'liquidado');
        if ($turn == 'rotativo') {
            $query->whereExists(function ($query) use ($dates) {
                $query->select(DB::raw(1))
                    ->from('rotating_turn_hours as la')
                    ->whereColumn('la.person_id', 'p.id')
                    ->whereBetween(DB::raw('DATE(la.date)'), $dates);
            });
        }

        return $query->select('p.first_name', 'p.first_surname', 'p.id', 'p.image', 'w.turn_type')
            ->get();
    }

    public function funcionarioRotativo($filtroDiarioFecha)
    {
        $translateService = new TranslateService();
        $funcionario =  Person::with(['diariosTurnoRotativo' => $filtroDiarioFecha])
            ->with(['horariosTurnoRotativo' => $filtroDiarioFecha])
            ->find(request()->get('id'))->toArray();

        if (isset($funcionario['horarios_turno_rotativo'])) {

            $funcionario['daysWork'] = $funcionario['horarios_turno_rotativo'];
            foreach ($funcionario['diarios_turno_rotativo']  as  $diario) {
                foreach ($funcionario['horarios_turno_rotativo'] as $ky => $turno) {

                    if ($turno['date'] == $diario['date']) {
                        $diario['nombreDia'] = $translateService->translateDay(Carbon::create($diario['date'])->englishDayOfWeek);
                        $diario['turnoOficial'] = RotatingTurn::find($turno['rotating_turn_id'])->toArray();
                        array_push($funcionario['daysWork'][$ky], ['day' => $diario]);
                    }
                }
            }
            unset($funcionario['diarios_turno_rotativo']);
        } else {
            return false;
        }
        return $funcionario;
    }

    public function calcularExtras($funcionario)
    {
        //Tiempo Asignado
        $tiempoAsignado = Company::first()->toArray()['work_time'];

        foreach ($funcionario['daysWork'] as $ky => $day) {
            $descansa = false;
            switch ($descansa) {
                case true:
                    break;
                case false:
                    if (isset($day[0])) {

                        //Seteo valores
                        $toleranciaSalida = 0;
                        $laborado = $day[0]['day'];

                        $extras = new stdObject();
                        $extras->HorasExtrasNocturnas = 0;
                        $extras->HorasExtrasDiurnas = 0;
                        $extras->HorasExtrasNocturnasDominicales = 0;
                        $extras->HorasExtrasDiurnasDominicales = 0;
                        $extras->horasExtrasDominicalesFestivasNocturnas = 0;
                        $extras->horasExtrasDominicalesFestivasDiurnas = 0;


                        $recargos = new stdObject();
                        $recargos->horasRecargoDominicalNocturna = 0;
                        $recargos->horasRecargoNocturna = 0;
                        $recargos->horasRecargoFestivo = 0;
                        $recargos->horasRecargoDominicalDiurno = 0;

                        //Asistencia
                        $turno = Request()->get('tipo');
                        $leaveTime = $turno === 'Fijo' ? 'leave_time_two' : 'leave_time_one';


                        [$asistencia, $salida, $fechaSalida] = $this->Asistencia($laborado['date'], $laborado['entry_time_one'], $laborado[$leaveTime]);

                        #dd($laborado['entry_time_one']);
                        //Tiempo Laborado
                        $tiempoLaborado = 0;
                        ##!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! aca mejorar con los horarios
                        $tiempoLaborado = $this->workedTime($laborado, $asistencia, $salida);
                        ##!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                        /*    if($day['id'] == 10){
                            dd($laborado);
                        } */

                       /*  $fechaSalida = $this->getLeaveDate($laborado); */
                        //dd($fechaSalida);
                        $FinTurnoOficcial =  $asistencia->copy()->addMinutes($tiempoAsignado);
                        $FinTurnoReal =  $asistencia->copy()->addMinutes($tiempoLaborado);

                        
                        //Se arma el objecto que sera enviado como parametro
                        $argumentos = new stdObject();
                        $argumentos->tiempoAsignado = $tiempoAsignado;
                        $argumentos->toleranciaSalida = $toleranciaSalida;
                        $argumentos->tiempoLaborado = $tiempoLaborado;
                        $argumentos->FinTurnoOficcial = $FinTurnoOficcial;
                        $argumentos->FinTurnoReal = $FinTurnoReal;
                        $argumentos->fechaSalida = $fechaSalida;
                        $argumentos->asistencia = $asistencia;

                        //Compruebo si trabajo mas del tiempo fijado y la tolerancia (Si trabajó extras)
                        /*    dd($tiempoLaborado); */
                        if ($tiempoLaborado > ($tiempoAsignado + $toleranciaSalida)) {
                            $recargos = $this->getDataRecargo($argumentos);
                            $extras = $this->getDataExtra($argumentos);
                        }

                        if ($tiempoLaborado <= ($tiempoAsignado + $toleranciaSalida)) {
                            $recargos = $this->getDataRecargoSinExtras($argumentos);
                        }

                        $funcionario['daysWork'][$ky]['tiempoLaborado'] = roundInHalf($tiempoLaborado / 60);
                        $funcionario['daysWork'][$ky]['tiempoAsignado'] = roundInHalf($tiempoAsignado / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoDominicalNocturna'] = roundInHalf($recargos->horasRecargoDominicalNocturna / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoNocturna'] = roundInHalf($recargos->horasRecargoNocturna / 60);
                        $funcionario['daysWork'][$ky]['horasRecargoFestivo'] = roundInHalf($recargos->horasRecargoFestivo / 60);

                        $funcionario['daysWork'][$ky]['horasRecargoDominicalDiurno'] = roundInHalf($recargos->horasRecargoDominicalDiurno / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasNocturnas'] = roundInHalf($extras->HorasExtrasNocturnas / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasDiurnas'] = roundInHalf($extras->HorasExtrasDiurnas / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasNocturnasDominicales'] = roundInHalf($extras->HorasExtrasNocturnasDominicales / 60);
                        $funcionario['daysWork'][$ky]['HorasExtrasDiurnasDominicales'] = roundInHalf($extras->HorasExtrasDiurnasDominicales / 60);

                        $funcionario['daysWork'][$ky]['horasExtrasDominicalesFestivasNocturnas'] = roundInHalf($extras->horasExtrasDominicalesFestivasNocturnas / 60);
                        $funcionario['daysWork'][$ky]['horasExtrasDominicalesFestivasDiurnas'] = roundInHalf($extras->horasExtrasDominicalesFestivasDiurnas / 60);
                    } else {

                        // No trabajó

                    }
                    break;
            }
        }
        return $funcionario;
    }

    private function getLeaveDate($laborado)
    {
        $date = '';
        if ($laborado['leave_time_one']) {

            $date = $laborado['leave_date'] . $laborado['leave_time_one'];

        } elseif ($laborado['breack_time_one'] && !$laborado['breack_time_two']) {

            $date = $laborado['breack_one_date'] . $laborado['breack_time_one'];

        } elseif (isset($laborado['launch_time_one']) && $laborado['launch_time_one']) {

            $date = $laborado['launch_one_date'] . $laborado['launch_time_one'];
        }

        return  Carbon::parse($date);
    }
    public function workedTime($laborado, $asistencia, $salida)
    {
        /*  dd($laborado); */
        $diffTotal = 0;
        //si tiene hora 1 de launch
        if ($laborado['leave_time_one'] ) {
            $diffTotal += $asistencia->diffInMinutes($salida);
        } elseif  ($laborado['breack_time_one'] && !$laborado['breack_time_two'])  {

            $launchEntry = Carbon::parse($laborado['breack_one_date'] . $laborado['breack_time_one']);

            $diffTotal = $asistencia->diffInMinutes($launchEntry);

        } elseif (isset($laborado['launch_time_one']) && $laborado['launch_time_one']) {

            $launchEntry = Carbon::parse($laborado['launch_one_date'] . $laborado['launch_time_one']);

            $diffTotal = $asistencia->diffInMinutes($launchEntry);
        }

        //si tiene hora 2 de launch
        if (isset($laborado['launch_time_two']) && $laborado['launch_time_two']) {
            $launchEntry = Carbon::parse($laborado['launch_two_date'] . $laborado['launch_time_two']);
            $launchLe = Carbon::parse($laborado['breack_two_date'] . $laborado['breack_time_two']);
            if ($salida) {
                $diffTotal += $launchEntry->diffInMinutes($salida);
            }
        }


        if (isset($laborado['entry_time_two']) && Request()->get('tipo') == 'Fijo'  && $laborado['entry_time_two']) {
            $launchEntry = Carbon::parse($laborado['date'] . $laborado['entry_time_two']);
            $launchLe = Carbon::parse($laborado['date'] . $laborado['leave_time_one']);
            $diffTotal = $launchEntry->diffInMinutes($launchLe);
        }

        return $diffTotal;
    }



    public function Asistencia($fecha, $horaEntrada, $horaSalida)
    {


        $asistencia = Carbon::parse($fecha . $horaEntrada);
        $salida =  Carbon::parse($fecha . $horaSalida);
        $fechaSalida =  Carbon::parse($fecha .  $horaSalida);

        if (($horaSalida >=   '00:00:00' && $horaSalida <   $horaEntrada)) {
            $salida->addDay();
            $fechaSalida->addDay();
        }

        return [$asistencia, $salida,  $fechaSalida];
    }
    public function setTiempoExtra($cantidadEnMinutos)
    {
        $this->tiempoParaExtras - $cantidadEnMinutos;
    }



    public function funcionarioFijo($filtroDiarioFecha)
    {
        $translateService = new TranslateService();

        /*         $funcionario =  Person::with(['diariosTurnoRotativo' => $filtroDiarioFecha])
        ->with(['horariosTurnoRotativo' => $filtroDiarioFecha])
        ->find(request()->get('id'))->toArray(); */

        $funcionario =  Person::with(
            ['diariosTurnoFijo' => $filtroDiarioFecha],
        )
            ->with('contractultimate', function ($q) {
                $q->select('*')
                    ->with('fixedTurn.horariosTurnoFijo');
            })
            ->find(request()->get('id'))->toArray();;
        if (isset($funcionario['contractultimate']['fixed_turn']['horarios_turno_fijo'])) {
            $funcionario['turno_fijo'] = $funcionario['contractultimate']['fixed_turn'];
            unset($funcionario['contractultimate']);

            $funcionario['daysWork'] = $funcionario['turno_fijo']['horarios_turno_fijo'];

            foreach ($funcionario['diarios_turno_fijo']  as  $diario) {
                foreach ($funcionario['turno_fijo']['horarios_turno_fijo'] as $ky => $turno) {

                    if ($turno['day'] == $translateService->translateDay(Carbon::create($diario['date'])->englishDayOfWeek)) {
                        array_push($funcionario['daysWork'][$ky], ['day' => $diario]);
                    }
                }
            }

            unset($funcionario['diarios_turno_fijo']);
        } else {
            return response()->json('Horario no asignado ');
        }

        return $funcionario;
    }
}
