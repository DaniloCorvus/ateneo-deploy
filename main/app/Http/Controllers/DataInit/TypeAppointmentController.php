<?php

namespace App\Http\Controllers\DataInit;

use App\Http\Controllers\Controller;
use App\Models\TypeAppointment;
use App\Services\TypeAppointmentService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeAppointmentController extends Controller
{
    use ApiResponser;

    public function __construct(TypeAppointmentService $TypeAppointmentService)
    {
        $this->TypeAppointmentService = $TypeAppointmentService;
    }

    public function get()
    {
        try {
            $this->tipos = json_decode($this->TypeAppointmentService->get(), true);
            handlerTableCreate($this->tipos);
            return $this->success('Tabla creada Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function store()
    {
        try {
            $this->tipos = json_decode($this->TypeAppointmentService->get(), true);
            $this->handlerInsertTable($this->tipos);
            return $this->success('Datos insertados Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function handlerInsertTable($data)
    {
        foreach ($data as  $item) {
            if (gettype($item) != 'array') {
                dd('Necesitas un array');
            } else {
                $dataFormated = [];
                foreach ($item as $index =>  $value) {
                    if (gettype($value) == 'array') {
                        dd('No se puede insertar este dato');
                    } else {
                        $dataFormated[customSnakeCase($index)] = $value;
                    }
                }
            }

            TypeAppointment::create($dataFormated);
        }
    }
}
