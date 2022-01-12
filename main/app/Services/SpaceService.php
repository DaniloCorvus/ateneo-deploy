<?php

namespace App\Services;

use App\Models\Space;
use App\Traits\ConsumeService;
use Illuminate\Support\Facades\DB;

class SpaceService
{

    public function __construct()
    {
    }

    public function  statistics()
    {

        $r = DB::table('spaces')
            ->join('agendamientos', 'spaces.agendamiento_id', '=', 'agendamientos.id')
            ->select(DB::raw('COUNT(spaces.id) as total'))
            ->where('spaces.state','=','Activo')
            ->when(request()->get('appointmentId'), function ($q, $cond) {
                $q->where('agendamientos.type_agenda_id', $cond);
            })
            ->when(request()->get('subappointmentId'), function ($q, $cond) {
                $q->where('agendamientos.type_appointment_id', $cond);
            });
        $showAll = request()->get("show_all_data");
        $r->when(($showAll == "false" || !$showAll), function ($q) {
            $q->where("agendamientos.user_id", auth()->user()->id);
        });


        $dataDis = clone ($r);
        $dataAsig = clone ($r);
        $dataOpen = clone ($r);

        $disponibles = $dataDis->where('spaces.status', true)->count();
        $asignadas =  $dataAsig->where('spaces.status', false)->count();
        $abiertas = $dataOpen->count();


        $r = [
            [
                'icon' => 'fa fa-calendar-week',
                'title' => '#Agendas Aperturadas',
                'status'=>'',
                'value' => $abiertas
            ],
            [
                'icon' => 'fa fa-calendar-day',
                'title' => '#Agendas Disponibles',
                'status'=>'true',
                'value' => $disponibles
            ],
            [
                'icon' => 'fa fa-calendar-check',
                'title' => '#Agendas Ocupadas',
                'status'=>'false',
                'value' => $asignadas
            ]
        ];
        return $r;
    }

    public function statisticsDetail()
    {
    

        $r = DB::table('spaces')
            ->join('agendamientos', 'spaces.agendamiento_id', '=', 'agendamientos.id')
            ->join('specialities', 'agendamientos.speciality_id', '=', 'specialities.id')
            ->select('specialities.name', DB::raw('COUNT(spaces.id) as total'))

            ->when(request()->get('appointmentId'), function ($q, $cond) {
                $q->where('agendamientos.type_agenda_id', $cond);
            })

            ->when(request()->get('subappointmentId'), function ($q, $cond) {
                $q->where('agendamientos.type_appointment_id', $cond);
            })
            ->when((request()->get('status') ), function ($q, $cond) {

                $q->where('spaces.status','=',  filter_var($cond, FILTER_VALIDATE_BOOLEAN));
            })
            ->where('spaces.state','=','Activo')
            ->groupBy('specialities.id')
            ->orderBy('total','DESC')
            ->get();

            
        return $r;
    }
}
