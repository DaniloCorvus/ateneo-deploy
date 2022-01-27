<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

use function GuzzleHttp\Promise\all;

class MunicipalityController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Municipality::orderBy('name', 'DESC')
            ->when(Request()->get('department_id'), function ($q) {
                $params = explode(",", request()->get('department_id'));
                $q->whereIn('department_id', $params);
            })
            ->get(['name As text', 'id As value']);
        return $this->success($data);
    }

    public function allMunicipalities()
    {
        return $this->success(
            Municipality::all(['name as text', 'id as value'])
        );
    }
    
    
      public function paginate()
    {
        return $this->success(
            Municipality::orderBy('name')
            ->with([
                'department' => function($q){
                    $q->select('id','name')->when( Request()->get('department'), function($q, $fill){
                        $q->where('name', 'like','%'.$fill.'%');
                    });
                }
            ])
            ->whereHas('department',function($q){
                $q->when( request()->get('department'), function($q, $fill){
                
                    $q->where('name', 'like','%'.$fill.'%');
                });
            })
            ->when( Request()->get('code') , function($q, $fill)
            {
                $q->where('code','like','%'.$fill.'%');
            })
            ->when( Request()->get('name') , function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');
            })
            ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1))
        );
    }
    
    
    
     public function store(Request $request)
    {  
        try {
            $validate_code = Municipality::where('code', $request->get('code'))->first();
            if ($validate_code) {
                return $this->error('El código del Municipio ya existe', 423);
            }
            Municipality::updateOrCreate( [ 'id'=> $request->get('id')], $request->all());
            return $this->success('creacion exitosa');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 200);
        }
    }

}
