<?php

namespace App\Services;

use App\Traits\ConsumeService;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    use ConsumeService;

    /**
     * The base uri to be used to consume the authors service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the authors service
     * @var string
     */
    public $secret;


    public function __construct()
    {
        $this->baseUri = env('BASE_URI');
        $this->token = env('TOKEN');
    }

    /**
     * Get the full list of authors from the authors service
     * @return string
     */
    public function get()
    {
        $queryString = '{"MaxResults":25,"MatchAll":true,"PetitionID":null,"RoomID":null,"SiteID":null,"InstitutionID":28,"Institutions":null,"PatientInternalID":null,"PatientID":null,"PatientName":null,"ExamID":null,"OrderID":null,"Type":null,"Status":null,"ReportStatus":null,"Date":"/Date(1625893200000-0500)/","Date2":"/Date(1626065999999-0500)/","DateMatch":null,"ReminderSent":null,"Priority":null,"AssignedDr":null,"Specialities":null,"IncludeUnassigned":false,"ExcludeID":null}';
        return $this->performRequest('GET', '/service/AppointmentService.svc/appointments', $queryString);
    }

    static public function index()
    {

        $page = Request()->get('page');
        $page = $page ? $page : 1;

        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        return   DB::table('appointments')
            ->join('call_ins', 'call_ins.id', '=', 'appointments.call_id')
            ->join('patients', 'patients.identifier', '=', 'call_ins.Identificacion_Paciente')
            ->join('spaces', 'spaces.id', '=', 'appointments.space_id')
            ->join('people', 'people.id', '=', 'spaces.person_id')
            ->join('agendamientos', 'agendamientos.id', '=', 'spaces.agendamiento_id')
            ->join('type_appointments', 'type_appointments.id', '=', 'agendamientos.type_agenda_id')
            ->join('sub_type_appointments', 'sub_type_appointments.id', '=', 'agendamientos.type_appointment_id')
            ->join('specialities', 'specialities.id', '=', 'agendamientos.speciality_id')
            ->select(
                'appointments.*',
                DB::raw('DATE_FORMAT(spaces.hour_start, "%Y-%m-%d %h:%i %p") As hour_start'),
                DB::raw('Concat_ws(" ", people.first_name, people.first_surname) as profesional_name'),
                DB::raw('Concat_ws(" ", patients.firstname,  patients.surname,
                
                ' . DB::raw("FORMAT(patients.identifier, 0, '.')") . '
                
                ) as patient_name'),
                'specialities.name as speciality',
                'patients.phone as phone'
            )
            ->when((Request()->get('identifier') && Request()->get('identifier') != 'null'), function ($query) {
                $query->where('call_ins.Identificacion_Paciente', Request()->get('identifier'));
            })
            ->when(Request()->get('person_id'), function ($query, $id) {
                $query->where('people.id', '=', $id);
            })
            ->when(Request()->get('state'), function ($query, $state) {
                $query->where('appointments.state', '=', $state);
            })
            ->when(Request()->get('type_appointment_id'), function ($query, $state) {
                $query->where('type_appointments.id', $state);
            })
            ->when(Request()->get('sub_type_appointment_id'), function ($query, $state) {
                $query->where('sub_type_appointments.id', $state);
            })
            ->when(Request()->get('speciality_id'), function ($query, $state) {
                $query->where('agendamientos.speciality_id', $state);
            })
            ->when((Request()->get('company_id') && Request()->get('company_id') != 'null'), function ($query) {
                $query->where('agendamientos.ips_id',  Request()->get('company_id'));
            })
            ->when((Request()->get('location_id') &&  Request()->get('location_id') != 'null'), function ($query) {
                $query->where('agendamientos.location_id', Request()->get('location_id'));
            })
            ->when(Request()->get('space_date'), function ($query, $date) {
                $query->whereDate('spaces.hour_start', $date);
            })
            ->orderBy('spaces.hour_start', 'Asc')
            ->paginate($pageSize, '*', 'page', $page);
    }
}
