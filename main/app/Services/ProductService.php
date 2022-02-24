<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class ProductService
{

    public static function getProductsAccountPlans( $tipoCatalogo, $companyId ){
                
        return  DB::table('Producto as p')
                ->leftJoin('product_accounting_plans as pa','pa.product_id','p.Id_Producto')
                ->leftJoin('Plan_Cuentas as CI','CI.Id_Plan_Cuentas','pa.income_account_id')
                ->leftJoin('Plan_Cuentas as CG','CG.Id_Plan_Cuentas','pa.inventary_account_id')
                ->leftJoin('Plan_Cuentas as CCC','CCC.Id_Plan_Cuentas','pa.expensse_account_id')
                ->leftJoin('Plan_Cuentas as CE','CE.Id_Plan_Cuentas','pa.cost_account_id')
                ->leftJoin('Plan_Cuentas as CIV','CIV.Id_Plan_Cuentas','pa.entry_account_id')
                ->leftJoin('Plan_Cuentas as CIC','CIC.Id_Plan_Cuentas','pa.iva_sale_account_id')
                ->leftJoin('Plan_Cuentas as CDV','CDV.Id_Plan_Cuentas','pa.iva_buy_account_id')
                ->leftJoin('Plan_Cuentas as CDC','CDC.Id_Plan_Cuentas','pa.rete_fuente_buy_account_id')
                ->leftJoin('Plan_Cuentas as CRV','CRV.Id_Plan_Cuentas','pa.rete_ica_buy_account_id')
                ->select(
                    'pa.*',
                    'p.Id_Producto as product_id', 
                    'pa.id as product_accounting_plan_id' ,
                    'CI.Codigo as income_account',
                    'CG.Codigo as inventary_account',
                    'CCC.Codigo as expensse_account',
                    'CE.Codigo as cost_account',
                    'CIV.Codigo as entry_account',
                    'CIC.Codigo as iva_sale_account',
                    'CDV.Codigo as iva_buy_account',
                    'CDC.Codigo as rete_fuente_buy_account',
                    'CRV.Codigo as rete_ica_buy_account',
                    
                )->selectRaw('
                    CONCAT(
                        ifnull(p.Principio_Activo,""), " ",
                        ifnull(p.Presentacion,""), " ",
                        ifnull(p.Concentracion,""), " ",
                        ifnull(p.Nombre_Comercial,"")," ",
                        ifnull(p.Unidad_Medida,""), 
                        ifnull(p.Embalaje,"") 
                    ) as name
                     ')
                ->where('Tipo_Catalogo',$tipoCatalogo)
                ->where('p.Company_Id',$companyId)
            ->paginate( Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1));
    }
}