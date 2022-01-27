<?php

namespace App\Http\Controllers;

use App\Exports\DotationExport;
use App\Exports\DownloaDeliveriesExport;
use App\Models\InventaryDotation;
use App\Models\ProductDotationType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LateArrivalExport;



class InventaryDotationController extends Controller
{
    use ApiResponser;
    //

    public function index()
    {
        $page = Request()->get('page');
        $page = $page ? $page : 1;

        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        return $this->success(
            InventaryDotation::orderBy('id', 'DESC')->paginate($pageSize, '*', 'page', $page)
        );
    }

    public function getInventary()
    {
        $page = Request()->get('page');
        $page = $page ? $page : 1;
        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;
        /*
        $d = DB::table('inventary_dotations as ID')
            ->select('ID.stock', 'ID.code', 'ID.name', 'ID.size', 'ID.status', 'ID.type', 'ID.cost', 'ID.id')
            ->selectRaw('(SELECT IFNULL(SUM(quantity), 0)
                            FROM dotation_products DP
                            INNER JOIN dotations D ON D.id = DP.dotation_id
                            WHERE DP.inventary_dotation_id = ID.id
                            AND (D.delivery_state <> "Entregado" OR D.delivery_state <> "Anulado")) as cantidadA')
*/
        $d = DB::table('inventary_dotations as ID')
            ->Leftjoin('Inventario_Nuevo AS inu', 'inu.Id_Producto', '=', 'ID.product_id')
            ->Leftjoin('Costo_Promedio AS cp', 'cp.Id_Producto', '=', 'inu.Id_Producto')
            // ->select('cp.Costo_Promedio', 'ID.stock', 'ID.code', 'ID.name', 'ID.size', 'ID.status', 'ID.type', 'ID.cost', 'ID.id')
            ->select(
                DB::raw(' IFNULL(inu.Cantidad, 0) as CantidadInventario'),
                'cp.Costo_Promedio as cost',
                'inu.Cantidad',
                'inu.Cantidad_Apartada',
                'inu.Cantidad_Seleccionada',
                'ID.stock',
                'ID.product_id',
                'ID.code',
                'ID.name',
                'ID.size',
                'ID.status',
                'ID.type',
                'ID.id'
            )
            ->where('Cantidad', '>', 0)
            ->selectRaw('(SELECT IFNULL(SUM(Cantidad-(Cantidad_Apartada+Cantidad_Seleccionada)),0)
                          FROM Inventario_Nuevo WHERE Id_Producto = ID.product_id ) as cantidadA')

            ->when(Request()->get('type'),  function ($q, $fill) {
                $q->where('ID.type', $fill);
            })
            ->when(Request()->get('name'),  function ($q, $fill) {
                $q->where('ID.name', 'like', '%' . $fill . '%');
            })
/*
            ->when(Request()->get('entrega'),  function ($q, $fill) {

               // $q->havingRaw('ID.stock - cantidadA > 0');
                $q->havingRaw('Cantidad > 0');
                // $q->where('ID.type', $fill);
            })
*/
         //   ->where('inu.Cantidad', '>' )


            ->paginate($pageSize, '*', 'page', $page);

        return $this->success($d);
    }

    public function getTotatInventary()
    {
        $d = DB::table('inventary_dotations as id')
            ->selectRaw('pdt.name, SUM(id.stock) as value')

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

    public function getSelected()
    {

        $page = Request()->get('page');
        $page = $page ? $page : 1;
        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;
        $id = Request()->get('id');

        $d = DB::table('inventary_dotations as ID')
            ->select(
                DB::raw(' CONCAT(P.first_name," ",P.first_surname) as nameF '),
                'PD.created_at',
                'D.delivery_code',
                'PD.quantity',
                'D.type',
                'D.delivery_state'
            )
            ->join('dotation_products AS PD', 'PD.inventary_dotation_id', '=', 'ID.id')
            ->join('dotations AS D', 'D.id', '=', 'PD.Dotation_id')
            ->join('people AS  P', 'P.id', '=', 'D.person_id')

            ->where([
                ['ID.id', $id],
                ['D.delivery_state', '<>',  "Entregado"],
                ['D.delivery_state', '<>',  "Anulado"],
            ])
            ->paginate($pageSize, '*', 'page', $page);

        return $this->success($d);
    }


    public function getInventaryEpp()
    {
        $d = ProductDotationType::with('inventary')
            ->whereHas('inventary', function ($q) { {
                    $q->where('type', 'EPP');
                }
            })
            ->get();
        return $this->success($d);
    }

    public function indexGruopByCategory(Request $request)
    {
        $d = DB::select(
            'SELECT CPD.name ,SUM(stock) stock
            FROM inventary_dotations ID
            INNER JOIN product_dotation_types CPD
            ON ID.product_dotation_type_id = CPD.id
            GROUP BY ID.product_dotation_type_id ',
        );
        return $this->success($d);
    }


    public function statistics(Request $request)
    {

        $d = DB::table('dotations as D')
            ->selectRaw('IFNULL(SUM(D.cost), 0) as totalCostoMes, IFNULL(count(*),0) as totalMes')

            ->when(Request()->get('delivery'), function ($q, $fill) {
                $q->where('D.delivery_state', $fill);
            })

            ->when(Request()->get('cod'), function ($q, $fill) {
                $q->where('D.delivery_code', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('person'),  function ($q, $fill) {
                $q->where('D.user_id', $fill);
            })
            ->when(Request()->get('persontwo'),  function ($q, $fill) {
                $q->where('D.person_id', $fill);
            })

            ->when(Request()->get('firstDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '>=', $fill);
            })

            ->when(Request()->get('lastDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '<=', $fill);
            })

            ->when(Request()->get('type'),  function ($q, $fill) {
                $q->where('D.type', $fill);
            })->get();


        $dyear = DB::table('dotations as D')
            ->selectRaw('IFNULL(SUM(D.cost), 0) as totalCostoAnual, IFNULL(count(*),0) as totalAnual')

            ->when(Request()->get('delivery'), function ($q, $fill) {
                $q->where('D.delivery_state', $fill);
            })

            ->when(Request()->get('cod'), function ($q, $fill) {
                $q->where('D.delivery_code', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('person'),  function ($q, $fill) {
                $q->where('D.user_id', $fill);
            })

            ->when(Request()->get('persontwo'),  function ($q, $fill) {
                $q->where('D.person_id', $fill);
            })

            ->when(Request()->get('firstDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '>=', $fill);
            })

            ->when(Request()->get('lastDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '<=', $fill);
            })

            ->when(Request()->get('type'),  function ($q, $fill) {
                $q->where('D.type', $fill);
            })->get();



        $td = DB::table('dotations as D')
            ->selectRaw(' IFNULL(count(*),0) as totalDotacion')

            ->when(Request()->get('delivery'), function ($q, $fill) {
                $q->where('D.delivery_state', $fill);
            })

            ->when(Request()->get('cod'), function ($q, $fill) {
                $q->where('D.delivery_code', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('person'),  function ($q, $fill) {
                $q->where('D.user_id', $fill);
            })

            ->when(Request()->get('persontwo'),  function ($q, $fill) {
                $q->where('D.person_id', $fill);
            })


            ->when(Request()->get('type'),  function ($q, $fill) {
                $q->where('D.type', $fill);
            })
            ->where('D.type', 'Dotacion')
            ->get();

        $te = DB::table('dotations as D')
            ->selectRaw(' IFNULL(count(*),0) as totalEpp')

            ->when(Request()->get('delivery'), function ($q, $fill) {
                $q->where('D.delivery_state', $fill);
            })

            ->when(Request()->get('cod'), function ($q, $fill) {
                $q->where('D.delivery_code', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('person'),  function ($q, $fill) {
                $q->where('D.user_id', $fill);
            })

            ->when(Request()->get('persontwo'),  function ($q, $fill) {
                $q->where('D.person_id', $fill);
            })


            ->when(Request()->get('type'),  function ($q, $fill) {
                $q->where('D.type', $fill);
            })
            ->where('D.type', 'EPP')
            ->get();

        return $this->success(['month' => $d[0], 'year' => $dyear[0], 'td' => $td[0], 'te' => $te[0]]);
    }

    public function download($fechaInicio, $fechaFin, Request $req)
    {
        $dates = [$fechaInicio, $fechaFin];

        return Excel::download(new DotationExport($dates), 'inventario.xlsx');
        return 'asd';
    }

    public function downloadeliveries($fechaInicio, $fechaFin, Request $req)
    {
        $dates = [$fechaInicio, $fechaFin];

        return Excel::download(new DownloaDeliveriesExport($dates), 'downloadeliveries.xlsx');
        return 'asd';
    }
}
