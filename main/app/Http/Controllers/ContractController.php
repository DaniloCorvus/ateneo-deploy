<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Cup;
use App\Policy;
use App\Service;
use App\TechnicNote;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $contract = Contract::query();

        $contract->select(
            'id As value',
            'company_id',
            'regimen_id',
            DB::raw("Concat_ws(' ', code, '-' , name) As text")
        );

        // $contract->when(request()->get('department_id'), function (Builder $q) {
        //     $q->where('department_id', request()->get('department_id'));
        // });

        $contract->when(request()->get('eps_id'), function (Builder $q) {
            $q->where(function (Builder $q) {
                $q->where('administrator_id', request()->get('eps_id'))
                    ->orWhere('regimen_id', request()->get('regimen_id'));
            });
        });

        $contract->when(request()->get('company_id'), function (Builder $q) {
            $q->where('company_id', request()->get('company_id'));
        });


        $result = $contract->get();

        return $this->success($result);
    }


    public function paginate()
    {
        $contract = Contract::query();
        $contract->with(['company:id,name', 'administrator:id,name', 'regimentypes:id,name']);
        $result = $contract->get(['id', 'company_id', 'administrator_id', 'start_date', 'end_date', 'name', 'code', 'status']);
        return $this->success($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $contract =  Contract::create([
                // "id" => request()->get("id"),
                "name" => request()->get("name"),
                "code" => request()->get("code"),
                "number" => request()->get("number"),
                "administrator_id" => request()->get("administrator_id"),
                // "contract_type_id" => request()->get("contract_type"),
                "payment_modality" => request()->get("payment_modality"),
                // "payment_method_id" => request()->get("payment_method_id"),
                // "benefits_plan_id" => request()->get("benefits_plan_id"),
                "start_date" => request()->get("start_date"),
                "end_date" => request()->get("end_date"),
                "price" => request()->get("price"),
                // "price_list_id" => request()->get("price_list_id"),
                // "variation" => request()->get("variation"),
                // "regimen_id" => request()->get("regimen_id")
                "company_id" => request()->get("company_id")
            ]);

            $departments = request()->get("department_id");
            if ($departments[0] == 0) $departments = DB::table('departments')->pluck('id')->toArray();


            $municipalities = request()->get("municipality_id");
            if ($municipalities[0] == 0) {
                $municipalities = DB::table('municipalities')->whereIn('department_id', $departments)->pluck('id')->toArray();
            }

            // $companies = request()->get("company_id");
            // if ($companies[0] == 0) $companies = DB::table('companies')->pluck('id')->toArray();

            $regimens = request()->get("regimen_id");
            if ($regimens[0] == 0) $regimens = DB::table('regimen_types')->pluck('id')->toArray();

            $locations = request()->get("location_id");
            if ($locations[0] == 0) {
                $locations = DB::table('locations')->where('company_id', request()->get("company_id"))->pluck('id')->toArray();
            }

            $contract->departments()->sync($departments);
            $contract->municipalities()->sync($municipalities);
            // $contract->companies()->sync($companies);
            $contract->regimentypes()->sync($regimens);
            $contract->locations()->sync($locations);

            foreach (request()->get("poliza") as $poliza) {
                Policy::create([
                    "contract_id" =>  $contract->id,
                    "code" =>  $poliza["codigopoliza"],
                    "start" =>  $poliza["iniciopoliza"],
                    "end" =>  $poliza["finpoliza"],
                    "name" =>  $poliza["nombrepoliza"],
                    "coverage" =>  $poliza["coberturapoliza"],
                ]);
            }

            foreach (request()->get("technicalNote") as $technicalNote) {
                $newTechnicalNote = TechnicNote::create([
                    "contract_id" =>  $contract->id,
                    "start" =>  $technicalNote["techn_note_date_init"],
                    "end" =>  $technicalNote["techn_note_date_end"],
                    "anio" =>  $technicalNote["techn_note_year_cups"],
                    "is_active" => ($technicalNote["is_default"]) ? 1 : 0,
                ]);

                foreach ($technicalNote["cups"] as $service) {
                    $newService = Service::create([
                        "technic_note_id" =>  $newTechnicalNote->id,
                        "cup_id" =>  $service["namec"]["value"],
                        // "code" =>  $service["codec"],
                        "value" =>  $service["valor"],
                        "centro_costo_id" =>  $service["centro_costo_id"],
                        "frequency" =>  $service["frequency"],
                    ]);

                    $specialities = $service["speciality"];
                    if ($specialities[0] == 0) {
                        $specialities =  Cup::find($service["namec"]["value"])->specialities()->pluck('id')->toArray();
                    }

                    $newService->specialities()->sync($specialities);
                }
            }

            return response()->success('contrato creado correctamente ');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data =  Contract::with([
            'company:id,name',
            'regimentypes:id,name',
            'administrator:id,name',
            'departments:id,name',
            'municipalities:id,name',
            'locations:id,name',
            'technicNotes',
            'technicNotes.services',
            'technicNotes.services.specialities' => function ($q) {
                $q->select('specialities.id', 'specialities.name as text');
            },
            'technicNotes.services.cup' => function ($q) {
                $q->select('*', 'id as value',  DB::raw("CONCAT(code, ' - ' ,description) as text"));
            },
            'technicNotes.services.cup.specialities' => function ($q) {
                $q->select('specialities.id as value', 'specialities.name as text');
            },
            'policies'
        ])->find($id);
        return response()->success($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
