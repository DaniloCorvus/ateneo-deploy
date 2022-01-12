<?php

use Carbon\Carbon;

if (!function_exists('transformDate')) {

    function transformDate($date)
    {
        $partes = explode('/', $date);
        return  Carbon::create($partes[2], $partes[1], $partes[0]);
    }
}
