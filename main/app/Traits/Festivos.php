<?php

namespace App\Traits;

use App\Models\Festive;
use Illuminate\Support\Facades\DB;

trait Festivos
{

    public  $festivos;

    public  function getFestivos()
    {
        return  Festive::select('date')->pluck('date')->toArray();
    }
}
