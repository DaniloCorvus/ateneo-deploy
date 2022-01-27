<?php

namespace App\Http\Controllers;

use App\Formulario;
use App\FormularioResponse;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;

class FormularioController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Formulario  $formulario
     * @return \Illuminate\Http\Response
     */
    public function show(Formulario $formulario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Formulario  $formulario
     * @return \Illuminate\Http\Response
     */
    public function edit(Formulario $formulario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Formulario  $formulario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Formulario $formulario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Formulario  $formulario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Formulario $formulario)
    {
        //
    }

    /**
     * Get Form  by id
     * @param $id Form
     * @return  \Illuminate\Http\Response
     */
    public function getFormulario(Formulario $formulario)
    {
        return   $this->success($formulario);
    }

    /**
     * Get Form  by id
     * @param $id Form
     * @return  \Illuminate\Http\Response
     */
    public function saveResponse()
    {
        try {
            foreach (json_decode(request()->get('data')) as  $response) {
                FormularioResponse::create([
                    'formulario_id' => 1,
                    'question_id' => $this->verifyResponseType($response),
                    'company_id' => request()->get('idCompany'),
                    'sede_id' => request()->get('idSede'),
                    'response' => $response->Respuesta,
                ]);
            }
            return   $this->success('Datos guarados correctamente');
        } catch (\Throwable $e) {
            return  $this->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * verify from response type 
     * @param Object
     * @return Int
     */

    public function verifyResponseType(Object  $response): int
    {
        if (isset($response->formulario_id)) {
            return  $response->id;
        }

        if (isset($response->question_id)) {
            return  $response->question_id;
        }

        if (!isset($response->question_id) && !isset($response->question_id)) {
            throw new Exception("El tipo de pregunta no ha sido verificado", 400);
        }
    }
}
