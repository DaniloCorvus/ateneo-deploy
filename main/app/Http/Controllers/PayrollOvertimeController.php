<?php

namespace App\Http\Controllers;

use App\Models\PayrollOvertime;
use Illuminate\Http\Request;

class PayrollOvertimeController extends Controller
{
    public function horasExtrasPorcentajes()
    {
        return PayrollOvertime::pluck('percentage','prefix');
    }

}
