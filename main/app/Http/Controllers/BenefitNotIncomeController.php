<?php

namespace App\Http\Controllers;

use App\Models\BenefitNotIncome;
use App\Models\Person;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BenefitNotIncomeController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $person = Person::findOrFail( $request->get('person_id') );
        return $this->success(
            BenefitNotIncome::where('person_id', '=', $person->id)->with('ingreso')->get()
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
        try {
            //code...
            BenefitNotIncome::create($request->all());
            return $this->success('creado con éxito');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage(), 500);

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

       
        try {
            //code...
            DB::table('benefit_not_incomes')->where('id', '=', $id)->delete();
            return $this->success('eliminado con éxito');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage(), 500);

        }
    }
}
