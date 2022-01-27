<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ClinicalHistoryController extends Controller
{
       //
       use ApiResponser;

       public function index(Request $req)
       {
              $identifier = request()->get('identifier');
              $response = Http::withOptions([
                     'verify' => false,
              ])->get("https://lab.globho.com/api/integration/patient/$identifier/records?api_key=794340c3-4956-4763-b8ab-145da3510100&pagingnumber=0");

              return $this->success($response->json());
       }

       public function show(Request $req)
       {
              $id = request()->get('id');
              $response = Http::withOptions([
                     'verify' => false,
              ])->get("https://lab.globho.com/api/integration/records/$id?api_key=794340c3-4956-4763-b8ab-145da3510100");

              return $this->success($response->json());
       }
}
