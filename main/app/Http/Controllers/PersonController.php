<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessionalRequest;
use App\Models\Person;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function GuzzleHttp\Promise\all;

class PersonController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($company = 0, $speciality = 0)
    {
       return $this->success(
        Person::orderBy('first_name')
            ->whereHas('specialties', function ($q) use ($speciality) {
                $q->where('id', $speciality);
            })->get(['id As value', DB::raw('concat(first_name, " ", first_surname)  As text')])
        );
    }

    /**
     * Display a listing of the resource paginated.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginate()
    {
        $page =  Request()->get('page') ?  Request()->get('page') : 1;
        $pageSize = Request()->get('pageSize') ? Request()->get('pageSize') : 10;


        return $this->success(
            DB::table('people')
                ->leftJoin('people_types', 'people.people_type_id', '=', 'people_types.id')
                ->leftJoin('companies', 'people.company_id', '=', 'companies.id')
                ->select(
                    'people.id',
                    'people.identifier',
                    'people.image',
                    'people.status',
                    'people_types.name as pople_type',
                    'companies.name as company',
                    DB::raw('concat(people.first_name, " ", people.first_surname) as full_name ')
                )
                ->when(Request()->get('name'), function ($q,$fill) {
                    var_dump($fill);
                   /*  $q->orWhereRaw("CONCAT(`people.first_name`, ' ', `people.first_surname`) LIKE ?", ['%' . $fill . '%']) */
                    $q->where('people.identifier', 'like', '%' . $fill . '%');
                })
                ->paginate($pageSize, ['*'], 'page')

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
        //
        return $this->success($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function storeFromGlobho(ProfessionalRequest $request)
    {
        try {
            $person = Person::create($request->all());
            return $this->success(['Professional creado correctamente', $person]);
        } catch (\Throwable $th) {
            return $this->error(['No se pudo crear el professional', $th->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function updateFromGlobho()
    {
        try {
            
             $person = Person::firstWhere('identifier', request()->input('identifier'));
             
            if($person){
                
              $person->update(request()->all());
              
              return $this->success('Professional actualizado correctamente');
              
            }
            
            throw new \Exception('No se logró encontrar professional');
            
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
