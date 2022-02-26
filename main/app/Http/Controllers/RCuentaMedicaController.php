<?php

namespace App\Http\Controllers;

use App\CustomFacades\ImgUploadFacade;
use App\RCuentaMedica;
use Illuminate\Http\Request;

class RCuentaMedicaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res = RCuentaMedica::with('tercero')->paginate(30);
        return response()->success($res);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {

            if (request()->get('type') == 1) {
                $res = $this->saveRadicacionPerson();
            }
            if (request()->get('type') == 2) {
                $res = $this->saveRadicacionCompany();
            }

            return response()->success($res);
        } catch (\Throwable $th) {
            return response()->error($th->getMessage());
            throw $th;
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $radicacion = RCuentaMedica::find($id);
        $status = request()->get('status');

        if ($status  == 'Glosada') {
            $radicacion->valor_glosado =  request()->get('valor');
            $status = 'Aceptada';
        }

        $radicacion->status = $status;
        $radicacion->save();
        return response()->success($radicacion->save());
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

    public function saveRadicacionPerson()
    {

        $radicacion = new RCuentaMedica();

        $radicacion->third_part_id = request()->get('identificacion');
        $radicacion->factura = saveBase64File(request()->get('factura'), 'radicaciones/');
        $radicacion->rut = saveBase64File(request()->get('rut'), 'radicaciones/');
        $radicacion->seguridad_social = saveBase64File(request()->get('seguridad_social'), 'radicaciones/');
        $radicacion->documento = saveBase64File(request()->get('documento'), 'radicaciones/');
        $radicacion->certificado_bancario = saveBase64File(request()->get('certificado_bancario'), 'radicaciones/');
        $radicacion->type = request()->get('type');
        $radicacion->company_id = request()->get('company');

        $radicacion->numero_factura = request()->get('numero_factura');
        $radicacion->numero_pacientes = request()->get('numero_pacientes');
        $radicacion->observaciones = request()->get('observaciones', '');
        $radicacion->valor_factura = request()->get('valor_factura');
        return $radicacion->save();
    }

    public function saveRadicacionCompany()
    {
        $radicacion = new RCuentaMedica();

        $radicacion->third_part_id = request()->get('identificacion');
        $radicacion->factura = saveBase64File(request()->get('factura'), 'radicaciones/');
        $radicacion->rut = saveBase64File(request()->get('rut'), 'radicaciones/');
        $radicacion->seguridad_social = saveBase64File(request()->get('seguridad_social'), 'radicaciones/');
        $radicacion->certificado_bancario = saveBase64File(request()->get('certificado_bancario'), 'radicaciones/');
        $radicacion->camara_comercio = saveBase64File(request()->get('camara_comercio'), 'radicaciones/');
        $radicacion->type = request()->get('type');
        $radicacion->company_id = request()->get('company');
        $radicacion->numero_factura = request()->get('numero_factura');
        $radicacion->numero_pacientes = request()->get('numero_pacientes');
        $radicacion->observaciones = request()->get('observaciones', '');
        $radicacion->valor_factura = request()->get('valor_factura');

        return $radicacion->save();
    }
}
