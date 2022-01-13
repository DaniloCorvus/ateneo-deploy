<?php

namespace App\Http\Controllers;

use App\Models\DocumentTypes;
use App\Traits\ApiResponser;
use App\Models\TypeDocument;
use Illuminate\Http\Request;

class DocumentTypesController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(DocumentTypes::where('status', '=', 'Activo')
        ->get(['name As text', 'id As value']));
    }

    public function paginate()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize',$data) ? $data['pageSize'] : 10;
        return $this->success(
            DocumentTypes::when( Request()->get('name') , function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');
            })
            ->paginate($pageSize, ['*'],'page', $page));
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
        try {
            $typeDocument = DocumentTypes::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($typeDocument->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TypeDocument  $typeDocument
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentTypes $typeDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TypeDocument  $typeDocument
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentTypes $typeDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeDocument  $typeDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentTypes $typeDocument)
    {
        try {
            $typeDocument = DocumentTypes::find(request()->get('id'));
            $typeDocument->update(request()->all());
            return $this->success('Documento actualizado correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TypeDocument  $typeDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeDocument = DocumentTypes::findOrFail($id);
            $typeDocument->delete();
            return $this->success('Documento eliminado correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
