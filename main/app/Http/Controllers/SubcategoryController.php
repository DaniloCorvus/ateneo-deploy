<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Subcategory;
use App\Models\SubcategoryVariable;

class SubcategoryController extends Controller
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
        try {
            $data = $request->except(["dynamic"]);
            $subcategory = Subcategory::create($data);
            $dynamic = request()->get("dynamic");

            foreach($dynamic as $d){
				$d["subcategory_id"] = $subcategory->id;
				SubcategoryVariable::create($d);
			}
            return $this->success("guardado con éxito");

        } catch (\Throwable $th) {
            return $this->error(['message' => $th->getMessage(), $th->getLine(), $th->getFile()], 400);
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
        try {
            $data = $request->except(["dynamic"]);
            Subcategory::where('Id_Subcategoria', $id)->update($data);
            $dynamic = request()->get("dynamic");

            foreach($dynamic as $d){
				$d["subcategory_id"] = $id;
				SubcategoryVariable::updateOrCreate([ 'id'=> $d["id"] ], $d );
			}
            return $this->success("guardado con éxito");

        } catch (\Throwable $th) {
            return $this->error(['message' => $th->getMessage(), $th->getLine(), $th->getFile()], 400);
        }

    }

    public function deleteVariable($id){
        
        SubcategoryVariable::where("id", $id)->delete();

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
