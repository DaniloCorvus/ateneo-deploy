<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dotenv\Result\Success;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\AssignOp\Concat;

class ReporteController extends Controller
{
    public function general()
    {
        request()->get('typeReport');

        switch (request()->get('typeReport')) {
            case 'Reporte de agendas':
                $data =  ['type' => 'AgendasReport', 'data' => $this->AgendasReport(request()->all())];
                break;
            case 'Reporte de atenciones':
                $data =  ['type' => 'AttentionReport', 'data' => $this->AttentionReport(request()->all())];
                break;

            case 'Reporte de lista de espera':
                $data =  ['type' => 'WaitinListReport', 'data' => $this->WaitinListReport(request()->all())];
                break;

            case 'Reporte de estado de agendas':
                $data =  ['type' => 'AgendasStatus', 'data' => $this->AgendasStatus(request()->all())];
                break;
            default:
                break;
        }

        // return $data;

        return Excel::download(new InvoicesExport($data), $data['type'] . '.xlsx');
    }

    public function AgendasReport($request)
    {
        return DB::table('agendamientos')
            ->join('spaces', 'agendamientos.id', 'spaces.agendamiento_id')
            ->join('appointments', 'spaces.id', 'appointments.space_id')
            ->join('call_ins', 'call_ins.id', 'appointments.call_id')
            ->join('patients', 'patients.identifier', 'call_ins.Identificacion_Paciente')
            ->join('type_documents', 'type_documents.id', 'patients.type_document_id')
            ->join('municipalities', 'municipalities.id', 'patients.municipality_id')
            ->join('departments', 'departments.id', 'patients.department_id')
            ->join('administrators', 'administrators.id', 'patients.eps_id')
            ->join('regimen_types', 'regimen_types.id', 'patients.regimen_id')
            ->join('locations', 'locations.id', 'agendamientos.location_id')
            ->join('people As agente', 'agente.identifier', 'call_ins.Identificacion_Agente')
            ->join('people As doctor', 'doctor.id', 'agendamientos.person_id')
            ->join('type_appointments', 'type_appointments.id', 'agendamientos.type_agenda_id')
            ->join('specialities', 'specialities.id', 'agendamientos.speciality_id')
            ->join('cups', 'cups.id', 'appointments.procedure')
            ->join('cie10s', 'cie10s.id', 'appointments.diagnostico')

            ->when(request()->get('daterange') && request()->get('daterange') != 'undefined', function (Builder $q) {
                $dates = explode('-', request()->get('daterange'));
                $dateStart = transformDate($dates[0]);
                $dateEnd = transformDate($dates[1]);
                $q->whereBetween('date_start', [$dateStart, $dateEnd])
                    ->whereBetween('date_end', [$dateStart, $dateEnd]);
            })


            ->when(request()->get('company_id'),  function (Builder $q) {
                $q->where('patients.company_id', request()->get('company_id'));
            })

            ->when(request()->get('speciality_id'),  function (Builder $q) {
                $q->where('agendamientos.speciality_id', request()->get('speciality_id'));
            })

            ->when(request()->get('eps_id'),  function (Builder $q) {
                $q->where('patients.eps_id', request()->get('eps_id'));
            })

            ->when(request()->get('regimen_id'),  function (Builder $q) {
                $q->where('patients.regimen_id', request()->get('regimen_id'));
            })


            // ->when(request()->get('company_id'),  function (Builder $q) {
            //     $q->where('appointments.ips_id', request()->get('company_id'));
            // })

            // ->when(request()->get('speciality_id'),  function (Builder $q) {
            //     $q->where('agendamientos.speciality_id', request()->get('speciality_id'));
            // })

            ->select(

                'appointments.code As consecutivo',
                'type_documents.code as tipo_documnto',
                DB::raw('Concat_ws(" ",patients.firstname, patients.surname) As nombre'),
                'patients.date_of_birth As cumple',
                'patients.gener As sexo',
                'patients.phone As telefono',
                'patients.address As direccion',
                'municipalities.name As municipio',
                'departments.name As departamento',
                'administrators.name As eps',
                'regimen_types.name As regimen',
                'locations.name As lugar',
                'spaces.hour_start As fecha_cita',
                DB::raw('Concat_ws(" ",agente.first_name, agente.first_surname) As asigna'),
                'appointments.state As estado',
                DB::raw('Concat_ws(" ",doctor.first_name, doctor.first_surname) As doctor'),
                'type_appointments.name As consulta',
                'specialities.name As especialidad',
                'cups.code As cup',
                'cups.description As cup_name',
                'cie10s.description As diagnostico',
                'appointments.ips As ips_remisora',
                'appointments.profesional As professional_remisor',
                'appointments.speciality As speciality_remisor',
            )->get()->chunk(500);
    }

    public function AttentionReport($request)
    {
        // return DB::table('attentions')

        return DB::table('agendamientos')

            ->join('spaces', 'agendamientos.id', 'spaces.agendamiento_id')
            ->join('appointments', 'spaces.id', 'appointments.space_id')
            ->join('call_ins', 'call_ins.id', 'appointments.call_id')
            ->join('patients', 'patients.identifier', 'call_ins.Identificacion_Paciente')
            ->join('type_documents', 'type_documents.id', 'patients.type_document_id')
            ->join('municipalities', 'municipalities.id', 'patients.municipality_id')
            ->join('departments', 'departments.id', 'patients.department_id')
            ->join('administrators', 'administrators.id', 'patients.eps_id')
            ->join('regimen_types', 'regimen_types.id', 'patients.regimen_id')
            ->join('locations', 'locations.id', 'agendamientos.location_id')
            ->join('people As agente', 'agente.identifier', 'call_ins.Identificacion_Agente')
            ->join('people As doctor', 'doctor.id', 'agendamientos.person_id')
            ->join('type_appointments', 'type_appointments.id', 'agendamientos.type_agenda_id')
            ->join('specialities', 'specialities.id', 'agendamientos.speciality_id')
            ->join('cups', 'cups.id', 'appointments.procedure')
            ->join('cie10s', 'cie10s.id', 'appointments.diagnostico')

            ->when(request()->get('daterange') && request()->get('daterange') != 'undefined', function (Builder $q) {
                $dates = explode('-', request()->get('daterange'));
                $dateStart = transformDate($dates[0]);
                $dateEnd = transformDate($dates[1]);
                $q->whereBetween('date_start', [$dateStart, $dateEnd])
                    ->whereBetween('date_end', [$dateStart, $dateEnd]);
            })

            ->when(request()->get('company_id'),  function (Builder $q) {
                $q->where('patients.company_id', request()->get('company_id'));
            })

            ->when(request()->get('speciality_id'),  function (Builder $q) {
                $q->where('agendamientos.speciality_id', request()->get('speciality_id'));
            })

            ->when(request()->get('eps_id'),  function (Builder $q) {
                $q->where('patients.eps_id', request()->get('eps_id'));
            })

            ->when(request()->get('regimen_id'),  function (Builder $q) {
                $q->where('patients.regimen_id', request()->get('regimen_id'));
            })

            ->whereIn('appointments.state', ['Confirmado', 'SalaEspera', 'Agendado'])

            ->select(

                'appointments.code As consecutivo',
                'type_documents.code as tipo_documnto',
                DB::raw('Concat_ws(" ",patients.firstname, patients.surname) As nombre'),
                'patients.date_of_birth As cumple',
                'patients.gener As sexo',
                'patients.phone As telefono',
                'patients.address As direccion',
                'municipalities.name As municipio',
                'departments.name As departamento',
                'administrators.name As eps',
                'regimen_types.name As regimen',
                'locations.name As lugar',
                'spaces.hour_start As fecha_cita',
                DB::raw('Concat_ws(" ",agente.first_name, agente.first_surname) As asigna'),
                'appointments.state As estado',
                DB::raw('Concat_ws(" ",doctor.first_name, doctor.first_surname) As doctor'),
                'type_appointments.name As consulta',
                'specialities.name As especialidad',
                'cups.code As cup',
                'cups.description As cup_name',
                'cie10s.description As diagnostico',
                'appointments.ips As ips_remisora',
                'appointments.profesional As professional_remisor',
                'appointments.speciality As speciality_remisor',
            )->get();
    }

    public function WaitinListReport($request)
    {
        return DB::table('waiting_lists')
            ->join('specialities', 'specialities.id',  'waiting_lists.speciality_id')
            ->join('appointments', 'appointments.id',  'waiting_lists.appointment_id')
            ->join('call_ins', 'call_ins.id',  'appointments.call_id')
            ->join('patients', 'patients.identifier', 'call_ins.Identificacion_Paciente')
            ->join('type_documents', 'type_documents.id', 'patients.type_document_id')
            ->join('municipalities', 'municipalities.id', 'patients.municipality_id')
            ->join('departments', 'departments.id', 'patients.department_id')
            ->join('administrators', 'administrators.id', 'patients.eps_id')
            ->join('regimen_types', 'regimen_types.id', 'patients.regimen_id')
            ->join('companies', 'companies.id', 'patients.company_id')
            ->join('type_appointments', 'type_appointments.id',  'waiting_lists.type_appointment_id')
            ->join('sub_type_appointments', 'sub_type_appointments.id',  'waiting_lists.sub_type_appointment_id')

            ->when(request()->get('daterange') && request()->get('daterange') != 'undefined', function (Builder $q) {
                $dates = explode('-', request()->get('daterange'));
                $dateStart = transformDate($dates[0]);
                $dateEnd = transformDate($dates[1]);
                $q->whereBetween('waiting_lists.created_at', [$dateStart, $dateEnd]);
            })

            // ->when(request()->get('company_id'),  function (Builder $q) {
            //     $q->where('ips_id', request()->get('company_id'));
            // })

            // ->when(request()->get('speciality_id'),  function (Builder $q) {
            //     $q->where('agendamientos.speciality_id', request()->get('speciality_id'));
            // })

            ->when(request()->get('company_id'),  function (Builder $q) {
                $q->where('patients.company_id', request()->get('company_id'));
            })

            ->when(request()->get('speciality_id'),  function (Builder $q) {
                $q->where('agendamientos.speciality_id', request()->get('speciality_id'));
            })

            ->when(request()->get('eps_id'),  function (Builder $q) {
                $q->where('patients.eps_id', request()->get('eps_id'));
            })

            ->when(request()->get('regimen_id'),  function (Builder $q) {
                $q->where('patients.regimen_id', request()->get('regimen_id'));
            })


            ->whereNull('appointments.space_id')
            ->where('waiting_lists.state',  'Pendiente')


            ->select(
                'type_documents.code as tipo_documnto',
                'patients.identifier as patient_identifier',
                DB::raw('CONCAT(patients.firstname, " ", patients.surname) as patient_name'),
                'patients.gener As sexo',
                'patients.phone As telefono',
                'patients.address As direccion',
                'specialities.name as speciality',
                'municipalities.name As municipio',
                'departments.name As departamento',
                'administrators.name As eps',
                'regimen_types.name As regimen',
                'appointments.observation As observaciones',
                'appointments.created_at As fecha',
            )->get();
    }

    public function AgendasStatus($request)
    {
        return DB::table('agendamientos')

            ->join('spaces', 'agendamientos.id', 'spaces.agendamiento_id')
            ->join('locations', 'locations.id', 'agendamientos.location_id')
            ->join('people As doctor', 'doctor.id', 'agendamientos.person_id')
            ->join('specialities', 'specialities.id', 'agendamientos.speciality_id')
            ->join('companies', 'companies.id', 'agendamientos.ips_id')

            ->when(request()->get('daterange') && request()->get('daterange') != 'undefined', function (Builder $q) {
                $dates = explode('-', request()->get('daterange'));
                $dateStart = transformDate($dates[0]);
                $dateEnd = transformDate($dates[1]);
                $q->whereBetween('date_start', [$dateStart, $dateEnd])
                    ->whereBetween('date_end', [$dateStart, $dateEnd]);
            })

            ->when(request()->get('company_id'),  function (Builder $q) {
                $q->where('patients.company_id', request()->get('company_id'));
            })

            ->when(request()->get('speciality_id'),  function (Builder $q) {
                $q->where('agendamientos.speciality_id', request()->get('speciality_id'));
            })

            ->when(request()->get('eps_id'),  function (Builder $q) {
                $q->where('patients.eps_id', request()->get('eps_id'));
            })

            ->when(request()->get('regimen_id'),  function (Builder $q) {
                $q->where('patients.regimen_id', request()->get('regimen_id'));
            })


            // ->when(request()->get('company_id'),  function (Builder $q) {
            //     $q->where('ips_id', request()->get('company_id'));
            // })

            // ->when(request()->get('speciality_id'),  function (Builder $q) {
            //     $q->where('agendamientos.speciality_id', request()->get('speciality_id'));
            // })

            ->select(
                DB::raw('COUNT(spaces.id) as Espacios_Totales'),
                DB::raw('IF(spaces.status = 0,1,0) as Espacios_Ocupados'),
                DB::raw('IF(spaces.status = 1 AND spaces.state="Activo",1,0) as Espacios_Disponibles'),
                DB::raw('IF(spaces.status = 1 AND spaces.state="Cancelado",1,0) as Espacios_Cancelados'),
                'companies.name As company',
                'agendamientos.date_start As fecha_inicio',
                'agendamientos.date_end As fecha_finalizacion',
                'agendamientos.created_at As hora_creacion',
                'specialities.name As especialidad',
            )
            ->groupBy('agendamientos.id')
            ->get();
    }
}
