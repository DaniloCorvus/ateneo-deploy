<?php

namespace App\Http\Controllers;

use App\Models\DisabilityLeave;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class DisabilityLeaveController extends Controller
{
    use ApiResponser;
    //
    public function index()
    {
        return $this->success(DisabilityLeave::all(['id as value','concept as text']));
    }

    public function paginate()
    {
        return $this->success(
            DisabilityLeave::when( Request()->get('novelty') , function($q, $fill)
            {
                $q->where('novelty','like','%'.$fill.'%');
            }
        )
        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function store(Request $request)
    {
        try {
            $noveltyTypes  = DisabilityLeave::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($noveltyTypes->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
  
}
