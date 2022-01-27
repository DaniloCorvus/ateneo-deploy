<?php

namespace App\Http\Controllers\DataInit;

use App\Http\Controllers\Controller;
use App\Models\Ips;
use App\Services\IpsService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class IpsController extends Controller
{
    use ApiResponser;

    public function __construct(IpsService $IpsService)
    {
        $this->IpsService = $IpsService;
    }

    public function get()
    {
        try {
            $this->ips = json_decode($this->IpsService->get(), true);
            dd($this->ips);
            handlerTableCreate($this->ips);
            return $this->success('Tabla creada Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function store()
    {
        try {
            $this->ips = json_decode($this->IpsService->get(), true);
            $this->handlerInsertTable($this->ips);
            return $this->success('Datos insertados Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function handlerInsertTable($data)
    {
        foreach ($data as  $item) {
            dd( $item);
            if (gettype($item) != 'array') {
                dd('Necesitas un array');
            } else {
                $dataFormated = [];
                foreach ($item as $index =>  $value) {
                    if (gettype($value) == 'array') {
                        $this->handlerInsertTableRes($value, $index);
                    } else {
                        $dataFormated[customSnakeCase($index)] = $value;
                    }
                }
            }

            dd($dataFormated);

            // Ips::create($dataFormated);
        }
    }
    public function handlerInsertTableRes($data, $table)
    {
        if (count($data) > 0) {

            if ($table != 'EPSs' && $table != 'Interface' && $table != 'Parent' && $table != 'Regional') {
                dd($data, $table);
                // foreach ($data as  $item) {
                //     if (gettype($item) != 'array') {
                //         dd($item);
                //         dd('Necesitas un array');
                //     } else {
                //         $dataFormated = [];
                //         foreach ($item as $index =>  $value) {
                //             if (gettype($value) == 'array') {
                //                 dd('No se puede guardar este dato');
                //             } else {
                //                 $dataFormated[customSnakeCase($index)] = $value;
                //             }
                //         }
                //     }
                //     dd($dataFormated);
                //     Ips::create($dataFormated);
                // }
            }
        }
    }
}
