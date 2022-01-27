<?php

namespace App\Http\Controllers;

use App\Models\RrhhActivityType;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class RrhhActivityTypeController extends Controller
{
    //
    use ApiResponser;

    public function index()
    {
        return $this->success(RrhhActivityType::all());
    }

    public function store(Request $request)
    {
        try {
            RrhhActivityType::create($request->all());
            return $this->success('Actualización con éxtio');

            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage(),500);
        }
    }

    public function setState(Request $request){
        try {
            $type = RrhhActivityType::findOrFail( $request->get('id') );
            $type->state = $request->get('state');
            $type->save();
            return $this->success('Actualización con éxtio');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage(),500);

        }
    }
}
