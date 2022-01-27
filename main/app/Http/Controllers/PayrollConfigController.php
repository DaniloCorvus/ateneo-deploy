<?php

namespace App\Http\Controllers;

use App\Models\DisabilityLeave;
use App\Models\PayrollOvertime;
use App\Models\PayrollParafiscal;
use App\Models\PayrollRisksArl;
use App\Models\PayrollSocialSecurityCompany;
use App\Models\PayrollSocialSecurityPerson;
use App\PayrollDisabilityLeave;
use Illuminate\Http\Request;

class PayrollConfigController extends Controller
{
    //

    public function horasExtrasDatos()
    {
        return PayrollOvertime::all();
    }

    public function incapacidadesDatos()
    {
        return PayrollDisabilityLeave::all();
    }

    public function parafiscalesDatos()
    {
        return PayrollParafiscal::all();
    }

    public function riesgosArlDatos()
    {
        return PayrollRisksArl::all();
    }


    public function sSocialEmpresaDatos()
    {
        return PayrollSocialSecurityCompany::all();
    }
    public function sSocialFuncionarioDatos()
    {
        return PayrollSocialSecurityPerson::all();
    }
}
