<?php

namespace App\Http\Controllers;

use App\Models\Dotation;
use App\Models\DotationProduct;
use App\Models\InventaryDotation;
use App\Models\ProductDotationType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DotationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function index()
    {

        $page = Request()->get('page');
        $page = $page ? $page : 1;

        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        $d = DB::table('dotations AS D')
            ->join('dotation_products AS PD', 'PD.dotation_id', '=', 'D.id')
            ->join('inventary_dotations AS ID', 'ID.id', '=', 'PD.inventary_dotation_id')
            ->join('product_dotation_types AS GI', 'GI.id', '=', 'ID.product_dotation_type_id')
            ->join('people AS  P', 'P.id', '=', 'D.person_id')
            ->join('Usuario AS US', 'US.id', '=', 'D.user_id')
            ->join('people AS PF', 'PF.id', '=', 'US.person_id')
            ->select(
                DB::raw('GROUP_CONCAT( PD.quantity , " X  " , ID.name ) AS product_name'),
                DB::raw('GROUP_CONCAT( PD.inventary_dotation_id ) AS IID'),
                DB::raw('GROUP_CONCAT( PD.quantity ) AS quantity'),
                DB::raw(' SUM(PD.quantity * PD.cost) AS total'),
                DB::raw(' CONCAT(P.first_name," ",P.first_surname) as recibe '),
                DB::raw(' CONCAT(PF.first_name," ",PF.first_surname) as entrega '),
                'D.created_at',
                'D.id',
                'D.description',
                'D.state',
            )
            ->groupBy('D.id')
            ->orderBy('D.created_at', 'DESC')
            ->paginate($pageSize, '*', 'page', $page);

        return $this->success($d);
    }*/
    public function index()
    {

        $page = Request()->get('page');
        $page = $page ? $page : 1;
        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        $d = DB::table('dotations AS D')
            ->join('dotation_products AS PD', 'PD.dotation_id', '=', 'D.id')
            ->join('inventary_dotations AS ID', 'ID.id', '=', 'PD.inventary_dotation_id')
            ->join('product_dotation_types AS GI', 'GI.id', '=', 'ID.product_dotation_type_id')
            ->join('people AS  P', 'P.id', '=', 'D.person_id')
            ->join('users AS US', 'US.id', '=', 'D.user_id')
            ->join('people AS PF', 'PF.id', '=', 'US.person_id')
            ->select(
                DB::raw('GROUP_CONCAT( PD.quantity , " X  " , ID.name ) AS product_name'),
                DB::raw('GROUP_CONCAT( PD.inventary_dotation_id ) AS IID'),
                DB::raw('GROUP_CONCAT( PD.quantity ) AS quantity'),
                DB::raw(' SUM(PD.quantity * PD.cost) AS total'),
                DB::raw(' CONCAT(P.first_name," ",P.first_surname) as recibe '),
                DB::raw(' CONCAT(PF.first_name," ",PF.first_surname) as entrega '),
                'D.created_at',
                'D.id',
                'D.type',
                'D.delivery_code',
                'D.delivery_state',
                'D.description',
                'D.state',
            )
            ->when(Request()->get('type'), function ($q, $fill) {
                $q->where('D.type', 'like', '%' . $fill . '%');
            })
            ->when(Request()->get('name'), function ($q, $fill) {
                $q->where('ID.name', 'like', '%' . $fill . '%');
            })
            ->when(Request()->get('cod'), function ($q, $fill) {
                $q->where('D.delivery_code', 'like', '%' . $fill . '%');
            })
            ->when(Request()->get('description'), function ($q, $fill) {
                $q->where('D.description', 'like', '%' . $fill . '%');
            })
            ->when(Request()->get('type'), function ($q, $fill) {
                $q->where('D.type', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('person'), function ($q, $fill) {
                $q->where('D.user_id', $fill);
            })
            ->when(Request()->get('persontwo'), function ($q, $fill) {
                $q->where('D.person_id', $fill);
            })
            ->when(Request()->get('delivery'), function ($q, $fill) {
                $q->where('D.delivery_state', $fill);
            })
            ->when(Request()->get('firstDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '>=', $fill);
            })
            ->when(Request()->get('lastDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '<=', $fill);
            })

            ->groupBy('D.id')
            ->orderBy('D.created_at', 'DESC')
            ->paginate($pageSize, '*', 'page', $page);

        return $this->success($d);
    }
    /*
    public function getTotatlByTypes(Request $request)
    {
        $date = explode('-', $request->get('cantMes'));

        $d = DB::select('SELECT pdt.name, SUM(dp.quantity) as value
                FROM
                dotations d
                inner join dotation_products dp on dp.dotation_id = d.id
                inner join inventary_dotations id on id.id = dp.inventary_dotation_id
                INNER JOIN  product_dotation_types pdt on pdt.id = id.product_dotation_type_id
                where year(dispatched_at)= ' . $date[0] . '
                GROUP BY pdt.id');

        return $this->success($d);
    }
    */

    public function getTotatlByTypes(Request $request)
    {
        $d = DB::table('dotations as D')
            ->selectRaw('pdt.name, SUM(dp.quantity) as value')
            ->join('dotation_products AS dp', 'dp.dotation_id', '=', 'D.id')
            ->join('inventary_dotations AS id', 'id.id', '=', 'dp.inventary_dotation_id')
            ->join('product_dotation_types AS pdt', 'pdt.id', '=', 'id.product_dotation_type_id')


            ->when(Request()->get('person'),  function ($q, $fill) {
                $q->where('D.user_id', $fill);
            })
            ->when(Request()->get('persontwo'), function ($q, $fill) {
                $q->where('D.person_id', $fill);
            })

            ->when(Request()->get('cod'), function ($q, $fill) {
                $q->where('D.delivery_code', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('type'), function ($q, $fill) {
                $q->where('D.type', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('delivery'), function ($q, $fill) {
                $q->where('D.delivery_state', $fill);
            })


            ->when(Request()->get('firstDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '>=', $fill);
            })

            ->when(Request()->get('lastDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '<=', $fill);
            })
            ->groupBy('pdt.id')
            ->get();

        return $this->success($d);
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
    /*
    public function store(Request $request)
    {
        //

        try {
            //code...
            $entrega   = $request->get('entrega');
            $productos = $request->get('prods');

            $cost      = 0;
            $list_prods = '';
            $entrega['user_id'] = auth()->user()->id;
            $dotation = Dotation::create($entrega);

            foreach ($productos as $prod) {

                $cost      += $prod["quantity"] * $prod["cost"];
                $list_prods .= trim($prod["quantity"]) . ' x ' . trim($prod["name"]) . " | ";

                $inventary = InventaryDotation::find($prod['id']);
                $inventary->stock = $inventary->stock - (int)trim($prod["quantity"]);
                $inventary->save();
                $prodSave = $prod;

                $prodSave["inventary_dotation_id"] = $prodSave["id"];
                $prodSave["dotation_id"] = $dotation->id;
                DotationProduct::create($prodSave);
            }

            $entrega["Productos"] = trim($list_prods, " | ");
            $entrega["Costo"]     = $cost;

            $dotation->cost = $cost;
            $dotation->save();
            unset($oItem);
            return $this->success('guardado con éxito');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->success($th->getMessage(), 500);
        }
    }
    */
    public function store(Request $request)
    {
        try {

            $entrega   = $request->get('entrega');
            $productos = $request->get('prods');

            $cost      = 0;
            $list_prods = '';
            $entrega['user_id'] = auth()->user()->id;
            $entrega['delivery_state'] = 'Pendiente';

            $dotation = Dotation::create($entrega);
            $dotation = Dotation::find($dotation["id"]);
            $dotation->delivery_code = $entrega['type'] == 'Dotacion' ? 'ED-' . $dotation["id"] : 'EPP-' . $dotation["id"];
            $dotation->save();

            foreach ($productos as $prod) {

                $cost      += $prod["quantity"] * $prod["cost"];
                $list_prods .= trim($prod["quantity"]) . ' x ' . trim($prod["name"]) . " | ";
                $prodSave = $prod;
                $prodSave["inventary_dotation_id"] = $prodSave["id"];
                $prodSave["dotation_id"] = $dotation->id;
                DotationProduct::create($prodSave);
            }

            $entrega["Productos"] = trim($list_prods, " | ");
            $entrega["Costo"]     = $cost;

            $dotation->cost = $cost;
            $dotation->save();
            unset($oItem);
            return $this->success('guardado con éxito');
        } catch (\Throwable $th) {
            //throw $th;
         //   return $this->success($th->getMessage(), 500, $th->getLine());

            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);

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
        try {
            //code...
            $dotation = Dotation::find($id);
            $dotation->state = $request->get('state');
            $dotation->save();
            return $this->success('guardado con éxito');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->success($th->getMessage(), 500);
        }
    }

    public function approve(Request $request, $id)
    {
        //
        try {
            $dotation = Dotation::find($id);
            $dotation->delivery_state = $request->get('state');
            $dotation->save();

            return $this->success('guardado con éxito');
        } catch (\Throwable $th) {
            return $this->success($th->getMessage(), 500);
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

    public function statistics(Request $request)
    {
        $date = explode('-', $request->get('cantMes'));

        $d = DB::select('SELECT ifnull(count(*),0) as totalMes,
         ifnull(SUM(cost),0) as totalCostoMes
         FROM dotations
         where year(dispatched_at)= ' . $date[0] . '
               and
               month(dispatched_at)= ' . $date[1] . '
                AND state = "Activa"');

        $dyear = DB::select('SELECT count(*) as totalAnual,
         ifnull(SUM(cost),0) as totalCostoAnual
         FROM dotations
         where year(dispatched_at)= ' . $date[0] . ' AND state = "Activa"');

        return $this->success(['month' => $d[0], 'year' => $dyear[0]]);
    }


    public function getListProductsDotation(Request $request)
    {

        $code   = $request->get('code');
        $page = Request()->get('page');
        $page = $page ? $page : 1;
        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        $d = DB::table('dotation_products AS PD')
            ->select(
                'D.created_at',
                'D.id',
                'D.type',
                'D.delivery_code',
                'D.delivery_state',
                'D.description',
                'D.state',
                'ID.name as product_name',
                'PD.quantity',
                DB::raw(' CONCAT(P.first_name," ",P.first_surname) as recibe '),
                DB::raw(' CONCAT(PF.first_name," ",PF.first_surname) as entrega '),
            )
            ->join('dotations AS D', 'PD.dotation_id', '=', 'D.id')
            ->join('inventary_dotations AS ID', 'ID.id', '=', 'PD.inventary_dotation_id')
            ->join('product_dotation_types AS GI', 'GI.id', '=', 'ID.product_dotation_type_id')
            ->join('people AS  P', 'P.id', '=', 'D.person_id')
            ->join('users AS US', 'US.id', '=', 'D.user_id')
            ->join('people AS PF', 'PF.id', '=', 'US.person_id')

            ->where([
                ['PD.code', '' . $code . ''],
                ['PD.code', '' . $code . ''],
            ])
            ->paginate($pageSize, '*', 'page', $page);

        return $this->success($d);
    }

    public function getDotationType()
    {

        return $this->success(
            ProductDotationType::orderBy('id', 'ASC')->get(['name As text', 'id As value'])
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
}
