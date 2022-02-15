<?php

namespace App\Services;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

class DotationDownloadService
{
    static public function getDotation()
    {
        return DB::table('inventary_dotations as ID')
            ->select('ID.name', 'ID.size', 'ID.type', 'ID.status', 'ID.stock')
            ->when(Request()->get('type'),  function ($q, $fill) {
                $q->where('ID.type', $fill);
            })

            ->when(Request()->get('type'),  function ($q, $fill) {
                $q->where('ID.type', $fill);
            })
            ->when(Request()->get('name'),  function ($q, $fill) {
                $q->where('ID.name', 'like', '%' . $fill . '%');
            })

            ->when(Request()->get('entrega'),  function ($q, $fill) {

                $q->havingRaw('ID.stock - cantidadA > 0');
            })
            ->where('ID.stock', '>', 0)
            ->get();
    }


    static public function getDownloaDeliveries($dates)
    {
        return DB::table('dotations AS D')
            ->join('dotation_products AS PD', 'PD.dotation_id', '=', 'D.id')
            ->join('inventary_dotations AS ID', 'ID.id', '=', 'PD.inventary_dotation_id')
            ->join('product_dotation_types AS GI', 'GI.id', '=', 'ID.product_dotation_type_id')
            ->join('people AS  P', 'P.id', '=', 'D.person_id')
            ->join('users AS US', 'US.id', '=', 'D.user_id')
            ->join('people AS PF', 'PF.id', '=', 'US.person_id')
            ->select(
                'D.delivery_code',
                'D.type',
                'D.created_at',
                DB::raw('CONCAT(PF.first_name," ",PF.first_surname) as entrega '),
                DB::raw('CONCAT(P.first_name," ",P.first_surname) as recibe '),
                'D.description',
                DB::raw('GROUP_CONCAT( PD.quantity , " X  " , ID.name ) AS product_name'),
                DB::raw('SUM(PD.quantity * PD.cost) AS total'),
                'D.state',
                'D.delivery_state',
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
            ->when($dates, function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '>=', $fill[0]);
            })
            ->when($dates, function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '<=', $fill[1]);
            })
            ->groupBy('D.id')
            ->get();
    }
}
