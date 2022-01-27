<?php

namespace App\Http\Controllers\DataInit;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Services\EspecialidadService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    use ApiResponser;

    public function __construct(EspecialidadService $especialidadService)
    {
        $this->especialidadService = $especialidadService;
    }

    public function get()
    {
        try {
            $this->especialidades = json_decode($this->especialidadService->get(), true);
            handlerTableCreate($this->especialidades['Data']);
            return $this->success('Tabla creada Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function store()
    {
        try {
            $this->especialidades = json_decode($this->especialidadService->get(), true);
            $this->handlerInsertTable($this->especialidades);
            return $this->success('Datos insertados Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function handlerInsertTable($data)
    {
        foreach ($data as  $item) {
            if (gettype($item) != 'array') {
                // dd('Necesitas un array');
            } else {
                $dataFormated = [];
                foreach ($item as $index =>  $value) {
                    if (gettype($value) == 'array') {
                        $this->handlerInsertTableRespaldo($value, $index);
                    } else {
                        $dataFormated[customSnakeCase($index)] = $value;
                    }
                }
            }
            // Especialidad::create($dataFormated);
        }
    }
    public function handlerInsertTableRespaldo($data, $table)
    {
        if (count($data) > 0) {
            if ($table != 'EPSs' && $table != 'Interface' && $table != 'Parent' && $table != 'Regional') {
                $dataFormated = [];
                foreach ($data as $index =>  $value) {
                    if (gettype($value) == 'array') {
                        dd('Otro array');
                    } else {

                        //para especialidades intentando obtener los cups
                        if ($index == 'ID') {
                            $dataFormated['code'] =  $value;
                        }
                        /*****************************************************/

                        $dataFormated[customSnakeCase($index)] = $value;
                    }
                }
                Especialidad::create($dataFormated);
            }
        }
    }
}
