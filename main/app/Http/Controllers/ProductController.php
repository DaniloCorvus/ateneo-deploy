<?php

namespace App\Http\Controllers;

use App\Models\InventaryDotation;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Product;
use App\Models\VariableProduct;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        $tipoCatalogo = Request()->get('tipo');

        $data = DB::table('Producto as p')->join('Subcategoria as s', 's.Id_Subcategoria', 'p.Id_Subcategoria')
            ->join('Categoria_Nueva as c', 'c.Id_Categoria_Nueva', 's.Id_Categoria_Nueva')
            ->select(
                'p.Id_Producto',
                'p.Codigo_Cum',
                'p.Codigo_Cum as Cum',
                'p.Principio_Activo',
                'p.Descripcion_ATC',
                'p.Codigo_Barras',
                'p.Id_Producto',
                'p.Id_Categoria',
                'p.Id_Subcategoria',
                'p.Laboratorio_Generico as Generico',
                'p.Laboratorio_Comercial as Comercial',
                'p.Invima as Invima',
                'p.Imagen as Foto',
                'p.Nombre_Comercial as Nombre_Comercial',
                'p.Id_Producto',
                'p.Embalaje',
                'p.Tipo as Tipo',
                'p.Tipo_Catalogo',
                'p.Id_Tipo_Activo_Fijo',
                'p.Estado',
                'p.Referencia'
            );


        /*  if ($tipoCatalogo == 'Medicamento' || $tipoCatalogo == 'Material' ) { */
        # code...
        $data->selectRaw('
            CONCAT(
                ifnull(p.Principio_Activo,""), " ",
                ifnull(p.Presentacion,""), " ",
                ifnull(p.Concentracion,""), " ",
                ifnull(p.Nombre_Comercial,"")," ",
                ifnull(p.Unidad_Medida,""),
                ifnull(p.Embalaje,"")
                ) as Nombre,

                s.Nombre as Subcategoria,
                c.Nombre as Categoria

                 ');
        /*    } */



        return $this->success(
            $data->when(request()->get("tipo"), function ($q, $fill) {
                $q->where("p.Tipo_Catalogo", $fill);
            })
                ->when(request()->get("company_id"), function ($q, $fill) {
                    $q->where("p.company_id", $fill);
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
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
    public function store(Request $request)
    {
        try {

            $data = $request->except(["dynamic"]);

            $dynamic = request()->get("dynamic");
            $product = Product::create($data);
            // echo json_encode($product);
            foreach ($dynamic as $d) {
                $d["product_id"] = $product->id;
                VariableProduct::create($d);
            }

            $data['product_id'] = $product->id;
            $data['product_dotation_type_id'] = $data["Producto_Dotation_Type_Id"];
            $data['name'] = $data["Nombre_Comercial"];
            $data['code'] = $data["Codigo"];
            $data['type'] = $data["Producto_Dotacion_Tipo"];
            $data['status'] = $data["Status"];
            $data['cost'] = 0;
            $data['stock'] = 0;

            $product = InventaryDotation::create($data);

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
            $data = $request->except(["dynamic","Status","Codigo","Producto_Dotacion_Tipo"]);
            $dynamic = request()->get("dynamic");
            // var_dump($dynamic);
            $product = Product::where('Id_Producto', $id)->update($data);

            foreach ($dynamic as $d) {
                $d['product_id'] = $id;
                VariableProduct::updateOrCreate(['id' => $d["id"]], $d);
            }

            return $this->success("guardado con éxito");
        } catch (\Throwable $th) {
            return $this->error(['message' => $th->getMessage(), $th->getLine(), $th->getFile()], 400);
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
