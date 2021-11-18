<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\Other;
use App\Models\Person;
use App\Models\TypeLocation;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;
use App\Models\WorkContract;

use function GuzzleHttp\Promise\all;

class CompanyController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($typeLocation = 0)
    {
        $brandShowCompany = 0;

        $companies = Company::query();

        // TODO: arregla la peticion de companies

        if (request()->get('owner')) {
            return response()->success($companies->where('type', true)->orderBy('short_name')->get(['short_name as text', 'id as value']));
        };

        if (request()->get('owner')) {
            $companies->where('type', $brandShowCompany);
            $companies->whereIn('category', ['IPS', 'SERVICIOS']);
            return response()->success($companies->orderBy('short_name')->get(['short_name as text', 'id as value']));
        }

        // $companies->when(request()->get('professional_id'), function ($q) {
        //     $companies = Person::findOrfail(request()->get('professional_id'))->restriction()->with('companies:id,name,type')->first(['restrictions.id']);
        //     $q->whereIn('id', $companies->companies->pluck('id'));
        // });

        if ($typeLocation &&  $typeLocation != 3) {
            $typeLocation = TypeLocation::findOrfail($typeLocation);
            return CompanyResource::collection($companies->get());

            // $brandShowCompany = $typeLocation->show_company_owners;
        }



        if (gettype($typeLocation) != 'object' && $typeLocation == 3) {
            return CompanyResource::collection($companies->get());
        }

        return $this->success(CompanyResource::collection($companies->where('type', $brandShowCompany)->get()));
    }

    public function getBasicData()
    {
        return $this->success(
            Company::with('arl')->with('bank')->first()
        );
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
            $work_contract = WorkContract::find($request->get('id'));
            $work_contract->update($request->all());
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }


    public function saveCompanyData(Request $request)
    {
        return $this->success(
            $company = Company::findOrFail($request->get('id')),
            $company->update($request->all())
        );
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
    }

    public function getCompanyBaseOnCity($municipalityId)
    {
        $data = Company::withWhereHas('locations', function ($q) use ($municipalityId) {
            $q->select('id As value', 'name As text', 'company_id');
            // ->where('city', $municipalityId);
        })
            ->get(['id As value', 'name As text', 'id']);

        // $data = DB::table('companies')
        //     ->selectRaw('Group_Concat(locations.id) As locationsId')
        //     ->join('locations', 'locations.company_id', 'companies.id')
        //     ->where('locations.city', $municipalityId)
        //     ->whereExists(function ($query)  use ($municipalityId) {
        //         $query->select(DB::raw(1))
        //             ->from('locations')
        //             ->whereRaw('companies.id = company_id')
        //             ->where('city', $municipalityId);
        //     })->groupBy('companies.id')
        //     ->toSql();


        // $data = Company::with(['locations' => function ($query) use ($municipalityId) {
        //     $query->select('id As value', 'name As text', 'company_id')
        //         ->where('city', $municipalityId);
        // }])->whereHas('locations', function ($query) use ($municipalityId) {
        //     $query->select('id As value', 'name As text')
        //         ->where('city', $municipalityId);
        // })->get(['id As value', 'name As text', 'id']);

        return $this->success($data);

        return CompanyResource::collection(Company::get());
        // return CompanyResource::collection(Company::where('type', $brandShowCompany)->get());

    }

    public function getGlobal()
    {
        return Company::with('payConfiguration')->with('arl')->first([
            'id', 'arl_id', 'payment_frequency', 'name as social_reason', 'tin as document_number',
            'transportation_assistance', 'base_salary', 'law_1607'
        ]);
    }
}
