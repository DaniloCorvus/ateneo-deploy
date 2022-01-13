<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Subcategory;
use App\Models\SubcategoryVariable;
use Illuminate\Support\Facades\DB;

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
        return $this->success(
			DB::table("subcategoria as s")
				->select("s.Nombre As text","s.Id_Subcategoria As value")
                ->join("categoria_nueva as c", "c.Id_Categoria_nueva", "s.Id_Categoria_nueva")
				->where("s.Id_Categoria_nueva", $id)
				->get()
		);

    }

    public function getFieldEdit($idproducto=null, $idSubcategoria){

        return $this->success(
            DB::select("SELECT SV.label, SV.type, VP.valor, SV.id AS subcategory_variables_id,  VP.id
            FROM subcategoria S
            INNER JOIN subcategory_variables SV  ON S.Id_Subcategoria = SV.subcategory_id
            LEFT JOIN variable_products VP ON VP.product_id = $idproducto and VP.subcategory_variables_id = SV.id
            WHERE S.Id_Subcategoria = $idSubcategoria")
        );

        // return $this->success(
        //     DB::select("SELECT SV.label, SV.type, VP.valor, S.Id_Subcategoria
        //     FROM subcategoria S
        //     INNER JOIN subcategory_variables SV  ON S.Id_Subcategoria = SV.subcategory_id
        //     LEFT JOIN variable_products VP ON VP.product_id = $idproducto and VP.subcategory_variables_id = SV.id
        //     where S.Id_Subcategoria = $idSubcategoria")
        // );
    }

    public function getField($id)
    {
        return $this->success(
			DB::table("subcategory_variables as sv")
				->select("sv.label","sv.type","sv.id")
                ->join("subcategoria as s", "s.Id_Subcategoria", "sv.subcategory_id")
				->where("sv.subcategory_id", $id)
				->get()
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
