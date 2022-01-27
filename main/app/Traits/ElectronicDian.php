<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

trait ElectronicDian
{

    public function cuneGenerate($data, $company, $softwarePin, $xmlType, $ambient)
    {
        $cune = $data['code'] . $data['date'] . $data['hour'] . $data['totals']['accrued'] .
            $data['totals']['deductions'] .  $data['totals']['voucher'] . $company->tin . $data['person']['identifier']
            .  $xmlType .  $softwarePin . $ambient;
        hash('sha384', $cune);
        return hash('sha384', $cune);
    }

    public function cuneDeleteGenerate($data, $company, $softwarePin, $xmlType, $ambient, $typeNote)
    {
        $ValDev = $typeNote == 1 ? $data['totals']['accrued'] : '0.00';
        $ValDed = $typeNote == 1 ? $data['totals']['deductions'] : '0.00';
        $ValTolNE = $typeNote == 1 ? $data['totals']['voucher'] : '0.00';
        $DocEmp = $typeNote == 1 ? $data['person']['identifier'] : '0';

        $cune = $data['code'] . $data['date'] . $data['hour'] . $ValDev .
            $ValDed .  $ValTolNE . $company->tin . $DocEmp
            .  $xmlType .  $softwarePin . $ambient;

        hash('sha384', $cune);
        return hash('sha384', $cune);
    }

    public function auth($company)
    {
        return   "Basic " . base64_encode($company->api_dian_user . ':' . $company->tin);
    }



    public function sendElectronicPayroll($body, $payroll, $note = false)
    {

        $data = [];
        try {

            $uriBase = 'http://api-dian.ateneoerp.com/api/ubl2.1/';
            $uriBase .=  $note ?  'payroll-note' : 'payroll/';

            $response = Http::accept('application/json')->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $this->auth($payroll->company)

            ])->post($uriBase, $body);
              $res = $response->json();


            /*        dd($res['ResponseDian']); */
            $result = $response["ResponseDian"]["Envelope"]["Body"]["SendNominaSyncResponse"]["SendNominaSyncResult"];
            $data["message"] = $result["StatusDescription"] . " - " . $result["StatusMessage"];
            $data["status"] = "succeded";
            $data['errors'] =  "";


            if ($result['IsValid']  == 'false') {
                $data["status"] = "rejected";
                foreach ($result["ErrorMessage"] as $e) {
                    $data['errors'] .= $e . " - ";
                }
            }
        } catch (\Throwable $th) {
            $data["status"] = "rejected";
            $data["message"] = "Fallo de coneccion";
            $data['errors'] = isset($res['message']) ? $res['message'] : '';
            //throw $th;
        }

        return $data;
    }
}
