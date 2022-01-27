<?php

namespace App\Http\Controllers;

use App\Models\InventaryDotationGroup;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventaryDotationGroupController extends Controller
{
    use ApiResponser;

    public function index()
    {
        return $this->success(
            InventaryDotationGroup::all()
        );
    }

    public function store(Request $req)
    {
        try {
            InventaryDotationGroup::create($req->all());
            return $this->success('Creado exitoso');
        } catch (\Throwable $th) {

            return $this->error($th->getMessage(), 500);
        }
    }

    public function indexByGruop(Request $request)
    {
        $id = $request->get('inventary_dotation_group_id');
        $group = $id ? (' , ID.inventary_dotation_group_id ') : '';
        $cond = $id ? ('WHERE  ID.inventary_dotation_group_id = ' . $id) : '';

        $d = DB::select(
            'SELECT CPD.name ,SUM(amount) amount
            FROM inventary_dotations ID
            INNER JOIN product_dotation_types CPD 
            ON ID.product_dotation_type_id = CPD.id
            ' . $cond . '
            GROUP BY ID.product_dotation_type_id ' . $group,
            [1]
        );
        return $this->success($d);
    }

    public function statistics(Request $request)
    {
        $d = DB::select('SELECT count(*) as CantidadMes,
         SUM(cost) as SumaMes 
         FROM dotations 
         where month(dispatched_at)= ' . $request->get('cantMes') . ' AND state = "Activa"');

        return $this->success($d ? $d[0] : ['SumaMes'=>0,'CantidadMes'=>0]);
    }
}
