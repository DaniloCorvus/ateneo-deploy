<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Exception;
use Illuminate\Http\Request;

class ServiceGlobhoController extends AppointmentController
{

    public function getInfoByGlobhoId()
    {
        $data =  Appointment::firstWhere('globo_id',  request()->get('globo_id'));
        return  $this->success($data);
    }

    public function updateStateByGlobhoId()
    {
        $code = request()->get('globo_id');
        $state = request()->get('state');

        try {

            if (!in_array($state, ['Agendado', 'Cancelado', 'Pendiente', 'SalaEspera', 'Asistio', 'Confirmado', 'NoAsistio']) && $code) {
                throw new Exception("No Existe codigo de appointment o estado");
            }

            $appointment = Appointment::with('space', 'callin', 'callin.patient', 'space.agendamiento')->firstWhere('globo_id', $code);

            if (!isset($appointment)) {
                throw new Exception('No se logra encontrar appointment');
            }

            
            $appointment->state = $state;
            $appointment->saveOrfail();

            if (in_array($state, ['Agendado', 'Cancelado', 'Asistio'])) {
                // $this->sendAppointmentModifiedNotification->handleMail($appointment);
            }

            return $this->success('Actualizacion correcta');
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }


    public function createFromGlobho(Request $request)
    {
        try {

            $appointment =  Appointment::create([
                'diagnostico' => request()->get('diagnostico'),
                'profesional' => request()->get('profesional'),
                'ips' => request()->get('ips'),
                'speciality' => request()->get('speciality'),
                'code' => $this->getCode(request()->get('tin')),
                'link' =>  '',
                'date' => request()->get('date'),
                'origin' =>     'Globo',
                'procedure' =>  request()->get('procedure'),
                'price' => '',
                'observation' => request()->get('observation'),

            ]);

            return $this->success(['Creacion correcta', $appointment], 201);
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }
}
