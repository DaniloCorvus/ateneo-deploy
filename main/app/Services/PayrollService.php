<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PayrollService
{


    static  public function getMes($fecha)
    {
        $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $f = explode("-", $fecha);
        $m = (int)$f[1] - 1;
        return $meses[$m] . " del " . $f[0];
    }
    static public function getQuincena()
    {
        $mes_hoy = date('Y-m');
        $dia_hoy = date('d');

        $nuevo_mes1 = strtotime("+1 months", strtotime($mes_hoy));
        $nuevo_mes2 = strtotime("+2 months", strtotime($mes_hoy));
       

        $mes_actual = static::getMes($mes_hoy);
        $mes_proximo1 = static::getMes(date('Y-m', $nuevo_mes1));
        $mes_proximo2 = static::getMes(date('Y-m', $nuevo_mes2));
        if ($dia_hoy <= 15) {
            $quincena[0]["Nombre"] = "1 Quincena de " . $mes_actual;
            $quincena[0]["Fecha"] = date("Y-m-15");
            $quincena[1]["Nombre"] = "2 Quincena de " . $mes_actual;
            $quincena[1]["Fecha"] = date("Y-m-") . date("d", (mktime(0, 0, 0, date("m") + 1, 1, date("Y")) - 1));
            $quincena[2]["Nombre"] = "1 Quincena de " . $mes_proximo1;
            $quincena[2]["Fecha"] = date('Y-m-15', $nuevo_mes1);
            $quincena[3]["Nombre"] = "2 Quincena de " . $mes_proximo1;
            $quincena[3]["Fecha"] = date('Y-m-', $nuevo_mes1) . date("d", (mktime(0, 0, 0, date("m", $nuevo_mes1) + 1, 1, date("Y", $nuevo_mes1)) - 1));
        } else {
            $quincena[0]["Nombre"] = "2 Quincena de " . $mes_actual;
            $quincena[0]["Fecha"] = date("Y-m-") . date("d", (mktime(0, 0, 0, date("m") + 1, 1, date("Y")) - 1));
            $quincena[1]["Nombre"] = "1 Quincena de " . $mes_proximo1;
            $quincena[1]["Fecha"] =  date('Y-m-15', $nuevo_mes1);
            $quincena[2]["Nombre"] = "2 Quincena de " . $mes_proximo1;
            $quincena[2]["Fecha"] = date('Y-m-', $nuevo_mes1) . date("d", (mktime(0, 0, 0, date("m", $nuevo_mes1) + 1, 1, date("Y", $nuevo_mes1)) - 1));
            $quincena[3]["Nombre"] = "1 Quincena de " . $mes_proximo2;
            $quincena[3]["Fecha"] = date('Y-m-15', $nuevo_mes2);
        }

        $response = $quincena;

        return $response;
    }
}
