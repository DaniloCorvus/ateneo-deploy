<?php

namespace App\Http\Libs\Nomina\Calculos;

use App\Models\NominaSeguridadSocialEmpresa;
use App\Models\NominaRiesgosArl;
use App\Models\Funcionario;
use App\Models\NominaParafiscales;
use App\Models\NominaSeguridadSocialFuncionario;
use App\Models\PayrollParafiscal;
use App\Models\PayrollRisksArl;
use App\Models\PayrollSocialSecurityCompany;
use App\Models\PayrollSocialSecurityPerson;
use App\Models\Person;

trait Porcentaje
{

    public function porcentajePension()
    {
        return PayrollSocialSecurityCompany::where('prefix', '=', 'pension')->first()['percentage'];
    }

    public function porcentajeSalud()
    {
        return PayrollSocialSecurityCompany::where('prefix', '=', 'salud')->first()['percentage'];
    }

    public function porcentajeRiesgosArl(Person $funcionario)
    {
        return PayrollRisksArl::where('id', $funcionario->payroll_risks_arl_id)->first()['percentage'];
    }

    public function porcentajeSena()
    {
        return PayrollParafiscal::where('prefix', '=', 'sena')->first()['percentage'];
    }

    public function porcentajeIcbf()
    {
        return PayrollParafiscal::where('prefix', '=', 'icbf')->first()['percentage'];
    }

    public function porcentajeCajaCompensacion()
    {
        return PayrollParafiscal::where('prefix', '=', 'caja_compensacion')->first()['percentage'];
    }

    public function porcentajePensionFunc()
    {
        return PayrollSocialSecurityPerson::where('prefix', '=', 'pension')->first()['percentage'];
    }

    public function porcentajeSaludFunc()
    {
        return PayrollSocialSecurityPerson::where('prefix', '=', 'salud')->first()['percentage'];
    }

    public function porcentajeFondoSolidaridad()
    {
        return PayrollSocialSecurityPerson::where('prefix', '=', 'fondo_solidaridad')->first()['percentage'];
    }

    public function porcentajesFondoSubsistencia()
    {
        return PayrollSocialSecurityPerson::pluck('percentage', 'prefix');
    }
}


