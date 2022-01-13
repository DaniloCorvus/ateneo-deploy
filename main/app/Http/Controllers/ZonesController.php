<?php

namespace App\Http\Controllers;

use App\Models\Zones;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ZonesController extends Controller
{
    use ApiResponser;

    public function allZones(){
        return $this->success(
            Zones::all(['name as text', 'id as value'])
        );    
    }

    public function index()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize',$data) ? $data['pageSize'] : 5;
        return $this->success(
            Zones::paginate($pageSize, ['*'],'page', $page)

        );
    }

    public function show($id)
    {
        return $this->success(
            Zones::find($id)
        );    
    }

    public function store( Request $request )
    {
        try {
            
            $zones = Zones::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($zones->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 200);
        }
    }




}
