<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\Other;
use App\Models\TypeLocation;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;

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

        if ($typeLocation &&  $typeLocation != 3) {

            $typeLocation = TypeLocation::findOrfail($typeLocation);
            $brandShowCompany = $typeLocation->show_company_owners;
            
        }

        if (gettype($typeLocation) != 'object' && $typeLocation == 3) {
            return CompanyResource::collection(Company::get());
        }

        return CompanyResource::collection(Company::where('type', $brandShowCompany)->get());
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
        //
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
            $q->select('id As value', 'name As text', 'company_id')
                ->where('city', $municipalityId);
        })->get(['id As value', 'name As text', 'id']);

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
}
