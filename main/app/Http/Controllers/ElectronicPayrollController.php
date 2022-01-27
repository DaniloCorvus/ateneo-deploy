<?php

namespace App\Http\Controllers;

use App\ElectronicPayroll;
use App\Models\Configuration;
use App\Models\PersonPayrollPayment;
use App\Traits\ApiResponser;
use App\Traits\ElectronicDian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectronicPayrollController extends Controller
{
    use ApiResponser;
    use ElectronicDian;

    //
    function paginate($payroll_id)
    {
        return $this->success(
            DB::table('person_payroll_payments as pp')
                ->join('people as f', 'f.id', 'pp.person_id')
                ->join('payroll_payments as p', 'p.id', 'pp.payroll_payment_id')
                ->select('pp.*')
                ->selectRaw('concat_ws(f.first_name,f.first_surname) as person_name')
                ->where('p.id', $payroll_id)
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }
    function statistics($payroll_id)
    {
        return $this->success(
            DB::table('person_payroll_payments')
                ->selectRaw('
                    SUM(IF(status = "rejected", 1 , 0) ) as rejected,
                    SUM(IF(status = "succeded", 1 , 0) ) as succeded,
                    SUM(IF(status = "modified", 1 , 0) ) as modified,
                    SUM(IF(status = "deleted", 1 , 0) ) as deleted,
                    SUM(IF(status IS NULL, 1 , 0) ) as pending
                ')
                ->groupBy('payroll_payment_id')
                ->where('payroll_payment_id', $payroll_id)
                ->first()
        );
    }

    function getElectronicPayroll($id)
    {
        return $this->success(
            ElectronicPayroll::where('person_payroll_payment_id', $id)->get()
        );
    }


    function deleteElectroincPayroll($id)
    {
        

        $personPayment = PersonPayrollPayment::with('payrollPayment')

            ->where('id', $id)->first();

        if ($personPayment->status != 'succeded') {
            return $this->errorResponse('La nómina que intenta eliminar no se encuetra Aceptada');
        }

        $data = [];

        $data['note_type'] = '2';
        $data['code_payroll'] = $personPayment->code;
        $data['cune_payroll'] = $personPayment->cune;
        $data['date_payroll'] = date('Y-m-d', strtotime($personPayment->created_at));

        $data['type_document_id'] = 8;
        $data['resolution_number'] = 2;

        $data['resolution_id'] = 7;

        $data['payroll_period'] = $personPayment->payrollPayment->payment_frequency;

        $data['date'] =  Carbon::now()->format('Y-m-d');
        $data['date_pay'] = Carbon::now()->format('Y-m-d');
        $data['hour'] =  Carbon::now()->format("H:i:s") . '-05:00';

        $data['observation'] = 'Se elimina la nota';

        $config = Configuration::where('company_id', $personPayment->payrollPayment->company->id)->first();
        $configData =  $config->consecutivoLevel1('Nomina', 'Nomina');

        $data['prefix'] = $configData->prefix;
        $data['number'] = $configData->number;
        $data['code'] = $configData->code;
        $data['file'] = $configData->code;
        $data['person']['identifier'] = '0';

        
        $data['cune_propio'] = $this->cuneDeleteGenerate($data, $personPayment->payrollPayment->company, '00777', '103', 1, 2);
        
        
        $responseDian =  $this->sendElectronicPayroll($data, $personPayment->payrollPayment,true);

        $payrollElectronic = new ElectronicPayroll();
        $payrollElectronic->status = $responseDian['status'];
        $payrollElectronic->message = $responseDian['message'];
        $payrollElectronic->errors = $responseDian['errors'];
        $payrollElectronic->person_payroll_payment_id = $personPayment->id;
        $payrollElectronic->cune = $responseDian['status'] == 'succeded' ?  $data['cune_propio'] : '';
        $payrollElectronic->code = $responseDian['status'] == 'succeded' ?  $data['code'] : '';
        $payrollElectronic->save();




        if ($responseDian['status'] == 'succeded') {
            $personPayment->status = 'deleted';
            $personPayment->cune = $payrollElectronic->cune;
            $personPayment->code = $payrollElectronic->code;

            $personPayment->electronic_reported = 1;
            $personPayment->electronic_reported_date = $data['date'];
            $personPayment->user_electronic_reported = auth()->user()->id;
            $personPayment->save();

            $config->savePrefix('Nomina');
        }


        if ($responseDian['status'] == 'rejected') {
            return $this->errorResponse('Se encontraron errores, por favor revise el historial');
        }
        return $this->success('Nomina eliminada correctamente');
    }
}
