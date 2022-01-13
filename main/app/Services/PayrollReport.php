<?php

namespace App\Services;

use Carbon\Carbon;

class PayrollReport
{

    static public function calculateWorkedDays($fecha1, $fecha2 , $europeo = true)
    {
        //try switch dates: min to max
        if ($fecha1 > $fecha2) {
            $temf = $fecha1;
            $fecha1 = $fecha2;
            $fecha2 = $temf;
        }

        list($yy1, $mm1, $dd1) = explode('-', $fecha1);
        list($yy2, $mm2, $dd2) = explode('-', $fecha2);

        if ($dd1 == 31) {
            $dd1 = 30;
        }

        if (!$europeo) {
            if (($dd1 == 30) and ($dd2 == 31)) {
                $dd2 = 30;
            } else {
                if ($dd2 == 31) {
                    $dd2 = 30;
                }
            }
        }

        if (($dd1 < 1) or ($dd2 < 1) or ($dd1 > 30) or ($dd2 > 31) or
            ($mm1 < 1) or ($mm2 < 1) or ($mm1 > 12) or ($mm2 > 12) or
            ($yy1 > $yy2)
        ) {
            return (-1);
        }
        if (($yy1 == $yy2) and ($mm1 > $mm2)) {
            return (-1);
        }
        if (($yy1 == $yy2) and ($mm1 == $mm2) and ($dd1 > $dd2)) {
            return (-1);
        }

        //Calc
        $yy = $yy2 - $yy1;
        $mm = $mm2 - $mm1;
        $dd = $dd2 - $dd1;

        return (($yy * 360) + ($mm * 30) + $dd);

    /*     return 25; */
    }
}
