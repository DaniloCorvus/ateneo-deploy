<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\ThirdPartyField;
use App\Models\ThirdPartyPerson;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ThirdPartyController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            ThirdParty::with('municipality')
            ->when( Request()->get('nit') , function($q, $fill)
            {
                $q->where('nit','like','%'.$fill.'%');
            })
            ->when( Request()->get('name') , function($q, $fill)
            {
                $q->where(DB::raw('concat(first_name," ",first_surname)'),'like','%'.$fill.'%');
            })->when( Request()->get('third_party_type') , function($q, $fill)
            {
                if (request()->get('third_party_type') == 'Todos') {
                    return null;
                } else {
                    $q->where('third_party_type','like','%'.$fill.'%');
                }
            })
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function thirdParties(){
        return $this->success(
            ThirdParty::select(
                DB::raw('concat(first_name," ",first_surname) as name')
            )
            ->get()
        );
    }

    public function getFields()
    {
        return $this->success(
            ThirdPartyField::where('state', '=', 'Activo')
            ->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except(["person"]);
        $typeImage = '.' . $request->typeImage;
        $data["image"] = URL::to('/') . '/api/image?path=' . saveBase64($data["image"], 'third_parties/', true, $typeImage);
        $typeRut = '.'. $request->typeRut;
        $base64 = saveBase64File( $data["rut"], 'thirdPartiesRut/', false, $typeRut);
        $data["rut"] = URL::to('/') . '/api/file?path=' . $base64;
        $people = request()->get('person');
        try {
            $thirdParty =  ThirdParty::create($data);
            foreach ($people as $person) {
                $person["third_party_id"] = $thirdParty->id;
                ThirdPartyPerson::create($person);
            }
            return $this->success('Guardado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getLine(), $th->getFile(), 500);
        }
    }

    public function changeState( Request $request ){
        try {
            $third = ThirdParty::find(request()->get('id'));
            $third->update($request->all());
            return $this->success('Proceso Correcto');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(
            ThirdParty::with('thirdPartyPerson')
            ->find($id)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except(["person"]);
        $typeImage = '.' . $request->typeImage;
        $data["image"] = URL::to('/') . '/api/image?path=' . saveBase64($data["image"], 'third_parties/', true, $typeImage);
        $typeRut = '.'. $request->typeRut;
        $base64 = saveBase64File( $data["rut"], 'thirdPartiesRut/', false, $typeRut);
        $data["rut"] = URL::to('/') . '/api/file?path=' . $base64;
        $people = request()->get('person');
        try {
            $thirdParty = ThirdParty::find($id)
            ->update($data);
            foreach ($people as $person) { 
                $thirdPerson = ThirdPartyPerson::find($person["id"]);
                $thirdPerson->update($person);
            }
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
