<?php

namespace App\Http\Controllers;

use App\Models\InventaryDotation;
use App\Models\ProductDotationType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $d = ProductDotationType::with('inventary')
            ->whereHas('inventary', function ($q) {
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


}
