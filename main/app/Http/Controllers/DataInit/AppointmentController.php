<?php

namespace App\Http\Controllers\DataInit;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use App\Traits\ApiResponser;
use App\Traits\HandlerContructTablePerson;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use ApiResponser;
    use HandlerContructTablePerson;

    public $appointmentService;
    public $appointments;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;;
    }

    public function store()
    {
        try {
            $this->appointments = json_decode($this->appointmentService->get(), true);
            return response($this->appointments);
            $this->handlerInsertTable($this->appointments);
            return $this->success('Datos insertados Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
