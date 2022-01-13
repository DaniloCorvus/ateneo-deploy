<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\ThirdPartyField;
use App\Traits\ApiResponser;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThirdPartyFieldController extends Controller
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
            ThirdPartyField::all()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    function buildFieldName($field_name){
		$palabras_campo = explode(" ", $field_name);
		$nueva_palabra = '';
		foreach ($palabras_campo as $palabra) {
			$p = strtolower($palabra);
			$nueva_palabra .= $p.'_';
		}
		return trim($nueva_palabra, "_");
	}

    public function store(Request $request)
    {
        try {
            $field = ThirdPartyField::create([
                'label' => $request->name,
                'name' => $this->buildFieldName($request->name),
                'type' => $request->type,
                'required' => $request->required,
                'length' => $request->length
            ]);
            if (isset($field)) {
                $type = '';
                if ($field->type == 'text') {
                    $type = ' VARCHAR(200)';
                } elseif($field->type == 'number') {
                    if ($field->length > 10) {
                        $type = ' BIGINT(20)';
                    } else {
                        $type = ' INT(20)';
                    }
                } elseif($field->type == 'date') {
                    $type = ' DATE';
                }
                $sql = DB::unprepared('ALTER TABLE `third_parties` ADD COLUMN'. ' '  . $this->buildFieldName($field->name) . $type . ' NULL DEFAULT NULL');
            }
            return ($field->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
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
        //
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
        //
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
