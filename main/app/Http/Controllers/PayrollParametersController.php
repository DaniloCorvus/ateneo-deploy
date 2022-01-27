<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PayrollParafiscal;
use App\Models\PayrollRisksArl;
use App\Models\PayrollSocialSecurityCompany;
use App\Models\Person;
use Illuminate\Http\Request;

class PayrollParametersController extends Controller
{
    //


    public function porcentajesSeguridadRiesgos($id)
    {
        $funcionario = Person::with('contractultimate')->findOrFail($id);
        
        $salarioMinimo = Company::first()['base_salary'];
        $riesgos = PayrollRisksArl::where('id',$funcionario->payroll_risks_arl_id)->pluck('percentage','prefix');
        $seguridad = PayrollSocialSecurityCompany::pluck('percentage','prefix');
        $parafiscales = PayrollParafiscal::pluck('percentage','prefix');

        if(Company::first(['law_1607']) && $funcionario->contractultimate->salary < ($salarioMinimo * 10)) {
           $seguridad['salud'] = 0;
           $parafiscales['sena'] = 0;
           $parafiscales['icbf'] = 0;
        }

        return $seguridad->merge(['riesgos'=>$riesgos->values()->first()])->merge($parafiscales);
    }
}
