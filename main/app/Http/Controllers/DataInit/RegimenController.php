<?php

namespace App\Http\Controllers\DataInit;

use App\Http\Controllers\Controller;
use App\Models\Regimen;
use App\Services\RegimenService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class RegimenController extends Controller
{

    use ApiResponser;

    public function __construct(RegimenService $RegimenService)
    {
        $this->RegimenService = $RegimenService;
    }

    public function get()
    {
        try {
            $this->regimenes = json_decode($this->RegimenService->get(), true);
            handlerTableCreate($this->regimenes);
            return $this->success('Tabla creada Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function store()
    {
        try {
            $this->regimenes = json_decode($this->RegimenService->get(), true);
            $this->handlerInsertTable($this->regimenes);
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

            Regimen::create($dataFormated);
        }
    }
}
