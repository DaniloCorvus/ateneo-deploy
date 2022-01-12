<?php

namespace App\Services;

use App\Traits\ConsumeService;
use Illuminate\Support\Facades\DB;

class WaitingListService
{
    use ConsumeService;

    public function __construct()
    {
    }

    static public function averageTime()
    {
        return  DB::table('waiting_lists')
            ->select(DB::raw(
                ' 
                            ( SUM( ifnull (TIMESTAMPDIFF( SECOND,  created_at , space_date_assign) ,0 ) ) /  count(*) ) time'
            ))
            ->where('state', '=', 'Agendado')
            ->first();
    }

    static public function getTopAwaitBySpeciality($limit = 5)
    {
        return  DB::table('waiting_lists')
            ->join('specialities', 'specialities.id', '=', 'waiting_lists.speciality_id')
            ->join('appointments', 'appointments.id', '=', 'waiting_lists.appointment_id')
            ->select(
                'specialities.name as speciality',
                DB::raw(
                    'COUNT(waiting_lists.speciality_id) as total'
                )
            )
            ->groupBy('waiting_lists.speciality_id')
            ->orderByDesc('total')
            ->whereNull('appointments.space_id')
            ->where('waiting_lists.state', '=', 'Pendiente')
            ->limit($limit)
            ->get();
    }


    static public function getLastAppointment()
    {
        return  DB::table('waiting_lists')
            ->join('specialities', 'specialities.id', '=', 'waiting_lists.speciality_id')
            ->join('appointments', 'appointments.id', '=', 'waiting_lists.appointment_id')
            ->join('call_ins', 'call_ins.id', '=', 'appointments.call_id')
            ->join('patients', 'patients.identifier', 'call_ins.Identificacion_Paciente')
            ->select(
                'specialities.name as speciality',
                'waiting_lists.created_at as date',
                DB::raw('waiting_lists.speciality_id as total', ''),
                DB::raw('CONCAT(patients.firstname, " ", patients.surname) as patient_name')

            )
            ->orderBy('waiting_lists.created_at')
            ->whereNull('appointments.space_id')
            ->where('waiting_lists.state', '=', 'Pendiente')
            ->first();
    }
    static public function index()
    {
        $page = Request()->get('page');
        $page = $page ? $page : 1;

        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        return DB::table('waiting_lists')
            ->join('specialities', 'specialities.id', '=', 'waiting_lists.speciality_id')
            ->join('appointments', 'appointments.id', '=', 'waiting_lists.appointment_id')
            ->join('call_ins', 'call_ins.id', '=', 'appointments.call_id')
            ->join('patients', 'patients.identifier', 'call_ins.Identificacion_Paciente')
            ->join('companies', 'companies.id', 'patients.company_id')
            ->join('type_appointments', 'type_appointments.id', '=', 'waiting_lists.type_appointment_id')
            ->join('sub_type_appointments', 'sub_type_appointments.id', '=', 'waiting_lists.sub_type_appointment_id')
            ->when(request()->get('date'), function ($q, $date) {
                $q->whereDate('waiting_lists.created_at', $date);
            })

            ->when((request()->get('patient') && request()->get('patient') != 'null'), function ($q) {
                $q->where(function ($query) {
                    $query->where('patients.identifier', request()->get('patient'))
                        ->orWhereRaw("CONCAT(`firstname`, ' ', `surname`) LIKE ?", ['%' . request()->get('patient')  . '%']);
                });
            })

            ->when((request()->get('speciality') && request()->get('speciality') != 'null'), function ($q) {
                $q->where('waiting_lists.speciality_id', request()->get('speciality'));
            })

            ->when((request()->get('institution') && request()->get('institution') != 'null'), function ($q) {
                $q->where('companies.id', request()->get('institution'));
            })

            ->whereNull('appointments.space_id')
            ->where('waiting_lists.state', '=', 'Pendiente')
            ->select(
                'waiting_lists.*',
                'specialities.name as speciality',
                'type_appointments.name as type',
                'sub_type_appointments.name as subType',
                'patients.identifier as patient_identifier',
                'patients.phone as patient_phone',
                DB::raw('CONCAT(patients.firstname, " ", patients.surname) as patient_name')
            )->paginate($pageSize, '*', 'page', $page);
    }
}
