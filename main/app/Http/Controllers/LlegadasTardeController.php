<?php

namespace App\Http\Controllers;

use App\Models\LlegadaTarde;
use App\Models\CentroCosto;
use App\Models\LateArrival;
use Barryvdh\DomPDF\Facade as PDF;

class LlegadasTardeController extends Controller
{

    public function getDatos($fechaInicio, $fechaFin)
    {
        $filtroFecha = function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        };

      /*   return CentroCosto::whereHas('dependencias' , function ($query) use ($filtroFecha) {
            $query->has('funcionarios')
            ->with(['funcionarios' => function ($query) use ($filtroFecha) {
                $query->select(['id', 'nombres', 'apellidos', 'image', 'dependencia_id'])
                ->with(['llegadasTarde' => $filtroFecha])->whereHas('llegadasTarde', $filtroFecha);
            }])->has('funcionarios.llegadasTarde');
        })->get(); */

     /*    return CentroCosto::whereHas(['dependencias' => function ($query) use ($filtroFecha) {
            $query->has('funcionarios')
            ->with(['funcionarios' => function ($query) use ($filtroFecha) {
                $query->select(['id', 'nombres', 'apellidos', 'image', 'dependencia_id'])
                ->with(['llegadasTarde' => $filtroFecha])->whereHas('llegadasTarde', $filtroFecha);
            }])->has('funcionarios.llegadasTarde');
        }])->get(); */


      /*    return CentroCosto::with(['dependencias' => function ($query) use ($filtroFecha) {
             $query->has('funcionarios')->with(['funcionarios' => function ($query) use ($filtroFecha) {
                 $query->select(['id', 'nombres', 'apellidos', 'image', 'dependencia_id'])->with(['llegadasTarde' => $filtroFecha])->whereHas('llegadasTarde', $filtroFecha);
             }])->has('funcionarios.llegadasTarde');
         }])->get(); */
    }

    public function llegadasPorFecha($fechaInicio, $fechaFin)
    {
        return LateArrival::whereBetween('fecha', [$fechaInicio, $fechaFin])->orderBy('fecha')->get()->groupBy('fecha');
    }

    public function reporteLlegadas($fechaInicio, $fechaFin)
    {

        return LateArrival::with('funcionario.dependencia.centroCosto')->whereBetween('fecha', [$fechaInicio, $fechaFin])->get(['id', 'funcionario_id', 'fecha', 'entrada_turno', 'entrada_real']);
    }

    public function reporteLlegadasPdf($fechaInicio, $fechaFin)
    {
        $llegadasTarde = LateArrival::with('funcionario.dependencia.centroCosto')->whereBetween('fecha', [$fechaInicio, $fechaFin])->orderBy('fecha')->get(['id', 'funcionario_id', 'fecha', 'entrada_turno', 'entrada_real']);



        // return view('reportes.pdf.llegadas_tarde', compact('llegadasTarde'));

      /*   $pdf = PDF::loadView('reportes.pdf.llegadas_tarde', compact('llegadasTarde')); */


       /*  return $pdf->download('Llegadas_tarde_reporte.pdf'); */
    }
    public static function guardarLlegadaTarde($datos)
    {
        
        LateArrival::create($datos);
        return true;
    }
}
