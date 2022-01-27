<?php

namespace App\Services;

use App\Models\Marcation;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\LlegadasTardeController as Llegadas;
use Carbon\Carbon;

class MarcationService
{

    static public function marcation($type, $fully, $id, $description)
    {
        Marcation::create([
            'type' => $type,
            'img' => $fully,
            'person_id' => $id,
            'description' => $description,
            'fecha' => date("Y-m-d H:i:s")
        ]);
    }

    static public function response($func, $title, $text, $icon)
    {
        return array(
            'title' => $title,
            'html' => "<img src='" . $func->image . "' class='img-thumbnail rounded-circle img-fluid' style='max-width:140px;'/><br><strong>
            $text </strong><br><strong>" . $func->first_name . " " . $func->first_surname . "</strong>",
            'icon' => $icon
        );
    }

    static public function sendEmail($func, $fully, $type, $hoy, $hactual, $empresa, $temp, $lleg = '')
    {
        $obj = new \stdClass();
        $obj->nombre = $func->first_name . " " . $func->first_surname;
        $obj->imagen = $fully;
        $obj->tipo = $type;
        $obj->hora = date("d/m/Y H:i:s", strtotime($hoy . " " . $hactual));
        $obj->ubicacion = 'entrada';
        $obj->destino = $func->email;
        /*  $obj->cargo = $func->cargo->nombre; */
        /** Datos Empresa */
        $obj->empresa = $empresa->razon_social;
        $obj->nit = $empresa->numero_documento;
        $obj->temperatura = $temp;
        $obj->tarde = $lleg;
        /** Fin Datos Empresa */
        // Mail::to($func->email)->send(new Correo($obj));
    }
    static public function makeLateArrival($id,  $hoy, $totalDuration, $hactual, $entry)
    {
        $datos_llegada = array(
            'person_id' => $id,
            'date' => $hoy,
            'time' => $totalDuration,
            'real_entry' => $hactual,
            'entry' => $entry
        );
        Llegadas::guardarLlegadaTarde($datos_llegada);
        /*          $datos_llegada = array(
                            'person_id' => $func->id,
                            'date' => $hoy,
                            'time' => $totalDuration,
                            'real_entry' => $hactual,
                            'entry' => $turno_asignado->entry_time
                        ); */
        
    }

    static public function makeTime($hoy,$hactual,$date1,$date2){
        $startTime = Carbon::parse($hoy . " " . $hactual);
        $finishTime = Carbon::parse($date1 . " " . $date2);
        return  $finishTime->diffInSeconds($startTime);
    }
}
