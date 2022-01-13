<?php

namespace App\Http\Controllers;

use App\Models\CallIn;
use App\Http\Requests\AppointmentRequest;
use App\Models\Patient;
use App\Traits\ApiResponser;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Log;
use App\Repositories\AppointmentRepository;
use App\Services\GlobhoService;

// include($_SERVER['DOCUMENT_ROOT'] .  DIRECTORY_SEPARATOR . '../elibom' . DIRECTORY_SEPARATOR . 'src/elibom_client.php');
include($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'main'  . DIRECTORY_SEPARATOR . 'elibom' . DIRECTORY_SEPARATOR . 'src/elibom_client.php');

use Elibom\APIClient\ElibomClient as ElibomClient;
use Illuminate\Database\Query\Builder;

class AppointmentController extends Controller
{

    use ApiResponser;

    public function appointmentRecursive(AppointmentRequest $request, AppointmentRepository $repository)
    {
        try {
            return $this->success($repository::recurrent());
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }

    public function confirmAppointment(AppointmentRepository $repository)
    {
        try {
            return $this->success($repository::confirm());
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }

    public function cleanInfo($data)
    {
        $callIn = CallIn::findOrfail($data);
        $callIn->status = 'Atendida';
        $callIn->save();
        return $this->success('Finalizado');
    }

    public function cancel(AppointmentRepository $repository, $id)
    {
        try {
            $repository::cancell($id);
            return $this->success('La cita se ha cancelado con èxito');
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }


    public function index()
    {
        try {
            return $this->success(AppointmentService::index());
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }

    public function toMigrate()
    {
        try {
            return $this->success(AppointmentService::toMigrate());
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }

    public function getDataCita($id)
    {
        try {
            return  Patient::findOrfail($id);;
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }


    public function getPending(AppointmentRepository $repository)
    {
        try {
            return $this->success($repository::pending());
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }

    public function getstatisticsByCollection(AppointmentRepository $repository)
    {
        try {
            return  $this->success($repository::getstatistics());
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }

    public function paginate()
    {
        try {
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }

    public function store(AppointmentRequest $request, AppointmentRepository $repository)
    {
        try {
            return $this->success($repository::store());
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }

    public function show(AppointmentRepository $repository, $appointment)
    {
        try {
            return response()->json($repository::show($appointment));
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }

    public function appointmentMigrate(GlobhoService $globhoService)
    {
        try {
            return response()->success($globhoService::sendGlobho(request()->get('id')));
        } catch (\Throwable $th) {
            Log::info([$th->getMessage(), $th->getLine()]);
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 400);
        }
    }
}
