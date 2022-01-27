<?php

namespace App\Http\Controllers\DataInit;


use App\Models\Agreement;
use App\Http\Controllers\Controller;
use App\Services\AgreementService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgreementController extends Controller
{

    use ApiResponser;


    public function __construct(AgreementService $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $this->agreements = json_decode($this->agreementService->get(), true);
            handlerTableCreate($this->agreements['Data']);
            return $this->success('Tabla creada Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
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
            $this->agreements = json_decode($this->agreementService->get(), true);
            $this->handlerInsertTable($this->agreements);
            return $this->success('Datos insertados Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function show(Agreement $agreement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function edit(Agreement $agreement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agreement $agreement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agreement $agreement)
    {
        //
    }


    public function handlerInsertTable($data)
    {
        foreach ($data as  $item) {
            if (gettype($item) != 'array') {
                // dd('Necesitas un array');
            } else {
                $dataFormated = [];
                foreach ($item as $index =>  $value) {
                    if (gettype($value) != 'array') {
                        $dataFormated[customSnakeCase($index)] = $value;
                        if ($index == 'Department') {
                            $dpto = DB::table('departamentos')->where('nombre', 'LIKE', '%' . normalize($value) . '%')->get()->first();
                            if ($dpto) {
                                $dataFormated['department_id'] =  $dpto->id;
                            }
                        }
                    } else {
                        if ($index == 'IPS') {
                            $companys = DB::table('companies')->get(['id', 'name']);
                            foreach ($companys as  $company) {
                                if (str_contains(normalize($company->name), $value['Name'])) {
                                    $dataFormated['company_id'] =  $company->id;
                                }
                            }
                        }
                    }
                }
            }
            $dataFormated['eps_id'] =  2;
            Agreement::create($dataFormated);
        }
    }
}
