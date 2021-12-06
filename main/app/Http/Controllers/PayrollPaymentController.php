<?php

namespace App\Http\Controllers;

use App\Models\PayrollPayment;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PayrollPaymentController extends Controller
{
    //
    use ApiResponser;
    /**
     * Retorna JSON todos los pagos de nómina hechos hasta la fecha
     *
     * @return Illuminate\Http\Response
     */
    public function getPagosNomina()
    {
        return $this->success(  PayrollPayment::all() );
    }
}
