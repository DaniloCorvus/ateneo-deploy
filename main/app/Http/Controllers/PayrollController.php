<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 180); //3 minutes

use App\Http\Libs\Nomina\Facades\NominaDeducciones;
use App\Http\Libs\Nomina\Facades\NominaIngresos;
use App\Http\Libs\Nomina\Facades\NominaNovedades;
use App\Http\Libs\Nomina\Facades\NominaPago;
use App\Http\Libs\Nomina\Facades\NominaProvisiones;
use App\Http\Libs\Nomina\Facades\NominaRetenciones;
use App\Http\Libs\Nomina\Facades\NominaSalario;
use App\Http\Libs\Nomina\Facades\NominaSeguridad;
use App\Models\Company;
use App\Models\Payroll;
use App\Models\PayrollOvertime;
use App\Models\PayrollPayment;
use App\Models\PayrollSocialSecurityPerson;
use App\Models\Person;
use App\Services\PayrollReport;
use App\Services\PayrollService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    use ApiResponser;

    //
    /**
     * Reporta a la dian
     *
     * @return Json
     */

    public function reportDian($id)
    {
        $payroll = PayrollPayment::findOrFail($id);
        $poplePay = $payroll->personPayrollPayment;

        $poplePay->each(function ($personPay) use ($payroll) {



            $data = collect();
            $data->type_document_id = 7;
            $data->resolution_number = 2;
            $data->date_pay = $payroll->created_at;
            $data->payroll_period = $payroll->payment_frequency;
            $data->cune_propio = 'cune55582asdasd223';
            $data->date_start_period = $payroll->start_period;
            $data->date_end_period = $payroll->end_period;

            $people = collect();

            $workContract = $personPay->person->contractultimate;
            if (!$workContract->reported_integraition_dian) {
                $data->integration_date = $workContract->date_of_admission;
                $people->contractultimate = 1;
                /* TODO activar */
                //$workContract->contractultimate->save();
            }

            $people->historic_worked_time = PayrollReport::calculateWorkedDays($workContract->date_of_admission, $payroll->end_period);
            $people->salary = $workContract->salary;
            $people->code = "W" . $personPay->person->identifier;

            /* TODO salario integral quemado */
            $people->salary_integral = true;
            $people->worker_type = collect();
            $people->worker_type->code = $workContract->worker_type_dian_code;

            /* TODO salario subtipo de salario */
            $people->worker_subtype = collect();
            $people->worker_subtype->code = "00";

            $people->work_contract_type = collect();
            /*   $tipoContrato =  $workContract->work_contract_type;
          */
            $people->work_contract_type->code = $workContract->work_contract_type->dian_code;


            $people->high_risk_pension = false;
            $people->identifier = $personPay->person->identifier;
            $people->first_name = $personPay->person->first_name;
            $people->middle_name = $personPay->person->second_name;
            $people->last_name = $personPay->person->first_surname;
            $people->last_names = $personPay->person->second_surname;

            $people->type_document_identification = collect();
            $people->type_document_identification->code = $personPay->person->documentType->dian_code;

            $people->work_place = collect();
            $people->work_place->country = collect();
            $people->work_place->country->code = "CO";

            /* TODO monicipio quemado */
            $people->work_place->municipality = collect();
            $people->work_place->country->code = "CO";



            dd($people);

            /* 
                "work_place": {

                    "municipality": {
                        "id": 1,
                        "code": "05001",
                        "department": {
                            "code": "05"
                        }
                    },
                    "addres": "prueba"
                }
         
          */


            dd($people);
            $data->people = $people;
        });
        return response($poplePay);
    }

    /*  public function store(PagosNominaStoreRequest $pagosNominaStoreRequest) */
    public function store()
    {

        try {

            $atributos = request()->all();

            $atributos['start_period'] = Carbon::parse(request()->get('start_period'))->format('Y-m-d');
            $atributos['end_period'] =  Carbon::parse(request()->get('end_period'))->format('Y-m-d');

            $atributos['payment_frequency'] = Company::get(['payment_frequency'])->first()['payment_frequency'];

            $funcionarios = Person::whereHas('contractultimate', function ($query) use ($atributos) {
                return $query->whereDate('date_of_admission', '<=', $atributos['end_period'])->whereDate('date_end', '>=', $atributos['start_period'])
                    ->orWhereNull('date_end')
                    ->where('liquidated', '0');;
            })->where('status', '!=', 'Liquidado')->get();

            $pagoNomina = PayrollPayment::create($atributos);

            $funcionarios->each(function ($funcionario) use ($pagoNomina) {

                $salario =  $this->getSalario($funcionario->id, $pagoNomina->start_period, $pagoNomina->end_period);
                $pagoNomina->personPayrollPayment()->create([
                    'person_id' => $funcionario->id,
                    'payroll_payment_id' => $pagoNomina->id,
                    'worked_days' => $salario['worked_days'],
                    'salary' => $salario['salary'],
                    'transportation_assistance' => $salario['transportation_assistance'],
                    'retentions_deductions' => $this->getRetenciones($funcionario->id, $pagoNomina->start_period, $pagoNomina->end_period)['valor_total'],
                    'net_salary' => $this->getPagoNeto($funcionario->id, $pagoNomina->start_period, $pagoNomina->end_period)['total_valor_neto'],
                ]);

                $previsiones =  $this->getProvisiones($funcionario->id, $pagoNomina->start_period, $pagoNomina->end_period);


                $pagoNomina->provisionsPersonPayrollPayment()->create([
                    'person_id' => $funcionario->id,
                    'payroll_payment_id' => $pagoNomina->id,
                    'severance' =>  $previsiones['resumen']['cesantias']['valor'],
                    'severance_interest' =>  $previsiones['resumen']['intereses_cesantias']['valor'],
                    'prima' =>  $previsiones['resumen']['prima']['valor'],
                    'vacations' =>  $previsiones['resumen']['vacaciones']['valor'],
                    'accumulated_vacations' =>   $previsiones['dias_vacaciones']['vacaciones_acumuladas_periodo'],
                    'total_provisions' =>  $previsiones['valor_total']
                ]);

                $seguridad =  $this->getSeguridad($funcionario->id, $pagoNomina->start_period, $pagoNomina->end_period);

                $pagoNomina->socialSecurityPersonPayrollPayment()->create([
                    'person_id' => $funcionario->id,
                    'payroll_payment_id' => $pagoNomina->id,
                    'health' =>  $seguridad['seguridad_social']['Salud'],
                    'pension' =>  $seguridad['seguridad_social']['Pensión'],
                    'risks' =>  $seguridad['seguridad_social']['Riesgos'],
                    'sena' => $seguridad['parafiscales']['Sena'],
                    'icbf' => $seguridad['parafiscales']['Icbf'],
                    'compensation_founds' => $seguridad['parafiscales']['Caja de compensación'],
                    'total_social_security' => $seguridad['valor_total_seguridad'],
                    'total_parafiscals' => $seguridad['valor_total_parafiscales'],
                    'total_social_security_parafiscals' => $seguridad['valor_total'],
                ]);
            });

            return $this->success('Nómina guardada correctamente',  $pagoNomina);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->errorResponse($th->getMessage(),  500);
        }
    }

    public function getFuncionario($identidad)
    {
        $funcionario = Person::where('id', '=', $identidad)->with('contractultimate')->first();
        if (!$funcionario) {
            return response()->json(['message' => 'Funcionario no encontrado'], 404);
        }

        return $funcionario;
    }

    function nextMonths()
    {
        try {
            return $this->success(PayrollService::getQuincena());
        } catch (\Throwable $th) {
            return $this->error($th->getMessage() . $th->getLine(), 402);
        }
    }

    /**
     * Retorna el resumen del pago de nómina en el mes actual, si ya existe en la BD entonces se modifica la  respuesta agregando propiedades a la respuesta
     *
     * @return Json
     */
    public function getPayrollPay($inicio = null, $fin = null)
    {

        $current = !$inicio ? true : false;
        $frecuenciaPago =  Company::get(['payment_frequency'])->first()['payment_frequency'];
        $pagoNomina = $nomina = $paga = $idNominaExistente = null;
        $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
        $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");

        $totalSalarios = 0;
        $totalRetenciones = 0;
        $totalSeguridadSocial = 0;
        $totalParafiscales = 0;
        $totalProvisiones = 0;
        $totalExtras = 0;
        $totalIngresos = 0;
        $totalCostoEmpresa = 0;

        if ($frecuenciaPago === 'Quincenal') {
            if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                $fechaInicioPeriodo = date("Y-m-16 00:00:00");
            } else {
                $fechaFinPeriodo = date("Y-m-15 23:59:59");
            }
        }

        $fechaInicioPeriodo = $fechaInicioPeriodo;
        $fechaFinPeriodo = $fechaFinPeriodo;


        /**
         * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
         * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
         * desde el componente de historial de pagos en Vue cuando se realiza una petición.
         */
        if ($inicio) {
            $fechaInicioPeriodo = $inicio;
        }

        if ($fin) {
            $fechaFinPeriodo = $fin;
        }



        /**
         * Comprobar si ya existe un pago de nómina en el periodo
         */
        if ($current) {
            # code...
            $nomina = PayrollPayment::latest()->first();
        } else {
            $nomina = PayrollPayment::whereDate('start_period', $fechaInicioPeriodo)
                ->whereDate('end_period', $fechaFinPeriodo)
                ->first();
        }
        if ($nomina) {
            $idNominaExistente = $nomina->id;
            $paga = $current ? Carbon::now()->between($nomina->start_period, $nomina->end_period) : true;
        }


        $fechasNovedades = function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereBetween('date_start', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('date_end', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('disability_leave');
        };

        $funcionarios = Person::whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereDate('date_of_admission', '<=', $fechaFinPeriodo)
                ->whereDate('date_end', '>=', $fechaInicioPeriodo)->orWhereNull('date_end')
                ->where('liquidated', '0');
        })->with(['payroll_factors' => $fechasNovedades])->take(1)->get(['id', 'identifier', 'first_name', 'first_surname', 'image']);
        try {
            //code...
            
            foreach ($funcionarios as $funcionario) {

                $tempSeguridad = $this->getSeguridad($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo);
                $seguridad = $tempSeguridad['valor_total_seguridad'];
                $parafiscal = $tempSeguridad['valor_total_parafiscales'];

                $tempExtras = $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo);
                $extras = $tempExtras['valor_total'];


                $salario = $this->getPagoNeto($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'];

                $retencion = $this->getRetenciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];
                $provision = $this->getProvisiones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];


                $temIngresos = $this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo);


                $totalSalarios +=  $salario;
                $totalRetenciones += $retencion;
                $totalSeguridadSocial += $seguridad;
                $totalParafiscales += $parafiscal;
                $totalProvisiones += $provision;
                $totalExtras += $extras;
                $totalIngresos += $temIngresos['valor_total'];

                $funcionariosResponse[] = [
                    'id' => $funcionario->id,
                    'identifier' => $funcionario->identifier,
                    'name' => $funcionario->first_name,
                    'surname' => $funcionario->first_surname,
                    'image' => $funcionario->image,
                    'salario_neto' => $salario,
                    'novedades' => [],
                    'novedades' => $funcionario->novedades,
                    'horas_extras' => $tempExtras['horas_reportadas'],
                    'novedades' => $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['novedades'],
                    'valor_ingresos_salariales' => ($temIngresos['valor_constitutivos'] +
                        $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'] +
                        $extras),
                    'valor_ingresos_no_salariales' => $temIngresos['valor_no_constitutivos'],
                    'valor_deducciones' => $this->getDeducciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']
                ];
            }



            $totalCostoEmpresa += $totalSalarios + $totalRetenciones +   $totalSeguridadSocial + $totalParafiscales + $totalProvisiones;


            return $this->success([
                'frecuencia_pago' => $frecuenciaPago,
                'inicio_periodo' => $fechaInicioPeriodo,
                'fin_periodo' => $fechaFinPeriodo,
                'salarios' =>  $totalSalarios,
                'seguridad_social' => $totalSeguridadSocial,
                'parafiscales' => $totalParafiscales,
                'provisiones' => $totalProvisiones,
                'extras' => $totalExtras,
                'ingresos' => $totalIngresos,
                'retenciones' => $totalRetenciones,
                'costo_total_empresa' => $totalCostoEmpresa,
                'nomina_paga' => $paga,
                'nomina_paga_id' => $idNominaExistente,
                'funcionarios' => $funcionariosResponse
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return [$th->getLine() . ' ' . $th->getFile() . ' ' . $th->getMessage()];
        }
    }

    public function payPeople($inicio = null, $fin = null)
    {
        try {

            $frecuenciaPago =  Company::get(['payment_frequency'])->first()['payment_frequency'];

            $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
            $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");
            $funcionariosResponse = [];

            if ($frecuenciaPago === 'Quincenal') {
                if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                    $fechaInicioPeriodo = date("Y-m-16 00:00:00");
                } else {
                    $fechaFinPeriodo =     date("Y-m-15 23:59:59");;
                }
            }

            $fechaInicioPeriodo = $fechaInicioPeriodo;
            $fechaFinPeriodo = $fechaFinPeriodo;

            /**
             * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
             * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
             * desde el componente de historial de pagos en Vue cuando se realiza una petición.
             */
            if ($inicio) {
                $fechaInicioPeriodo = $inicio;
            }

            if ($fin) {
                $fechaFinPeriodo = $fin;
            }
            $fechasNovedades = function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
                return $query->whereBetween('date_start', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('date_end', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('disability_leave');
            };
            $funcionarios = Person::whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
                return $query->whereDate('date_of_admission', '<=', $fechaFinPeriodo)
                    ->whereDate('date_end', '>=', $fechaInicioPeriodo)->orWhereNull('date_end')
                    ->where('liquidated', '0');
            })->with(['payroll_factors' => $fechasNovedades])->get();
            foreach ($funcionarios as $funcionario) {

                $funcionariosResponse[] = $this->success([
                    'id' => $funcionario->id,
                    'identifier' => $funcionario->identifier,
                    'first_name' => $funcionario->first_name,
                    'first_surname' => $funcionario->first_surname,
                    'image' => $funcionario->image,
                    'salario_neto' => $this->getPagoNeto($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'],
                    'novedades' => $funcionario->novedades,
                    'horas_extras' => $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['horas_reportadas'],
                    'novedades' => $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['novedades'],

                    'valor_ingresos_salariales' => ($this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_constitutivos'] +
                        $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'] +
                        $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']),
                    'valor_ingresos_no_salariales' => $this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_no_constitutivos'],
                    'valor_deducciones' => $this->getDeducciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']
                ]);
            }
            return $funcionariosResponse;
        } catch (\Throwable $th) {

            return [$th->getLine() . ' ' . $th->getFile() . ' ' . $th->getMessage()];
        }
    }

    public function getPagoNeto($id, $fechaInicio, $fechaFin)
    {
        return NominaPago::pagoFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    /**
     * Calcular la cantidad y total de horas extras y recargos acumulados del funcionario en el periodo
     *
     * @param int $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getExtrasTotales($id, $fechaInicio, $fechaFin)
    {

        return PayrollOvertime::extrasFuncionarioWithId($id)->fromTo($fechaInicio, $fechaFin);
    }

    /**
     * Calcular la cantidad y total de novedades del funcionario en el periodo
     *
     * @param int $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getNovedades($id, $fechaInicio, $fechaFin)
    {
        return NominaNovedades::novedadesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }


    public function getIngresos($id, $fechaInicio, $fechaFin)
    {

        return NominaIngresos::ingresosFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getDeducciones($id, $fechaInicio, $fechaFin)
    {

        return NominaDeducciones::deduccionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getSeguridad($id, $fechaInicio, $fechaFin)
    {

        return NominaSeguridad::seguridadFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getRetenciones($id, $fechaInicio, $fechaFin)
    {
        return  NominaRetenciones::retencionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getProvisiones($id, $fechaInicio, $fechaFin)
    {
        return NominaProvisiones::provisionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getSalario($id, $fechaInicio, $fechaFin)
    {

        return NominaSalario::salarioFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getPorcentajes()
    {
        return response(PayrollSocialSecurityPerson::select('concept', 'percentage')->cursor(), 200);
    }
}
