<?php

namespace App\Http\Controllers;

use App\Models\Lunch;
use App\Models\LunchPerson;
use App\Models\Person;
use App\Traits\ApiResponser;
use Faker\Calculator\Luhn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LunchControlller extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* return $this->success(
            Lunch::with('lunchPerson')->with([
                'user' => function($q){
                    $q->select('id', 'person_id');
                }
            ])->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        ); */
        return $this->success(
            DB::table('lunch_people as lp')
            ->select(
                'l.id',
                'lp.id as lunch_person_id',
                'l.value',
                'p.first_name',
                'p.first_surname',
                'l.state',
                'l.created_at',
                'lp.state as personState',
                DB::raw('concat(user.first_name," ",user.first_surname) as user')
            )
            ->when(request()->get('date'), function($q, $fill)
            {
                $q->where('l.created_at', 'like', '%'.$fill.'%');
            })
            ->when(request()->get('person'), function($q, $fill)
            {
                $q->where('lp.person_id', 'like', '%'.$fill.'%');
            })
            ->join('lunches as l', 'l.id', '=', 'lp.lunch_id')
            ->join('people as p', 'p.id', '=', 'lp.person_id')
            ->join('Usuario as u', 'u.id', '=', 'l.user_id')
            ->join('people as user', 'user.id', '=', 'u.person_id')
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
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
            $lunch = Lunch::create([
                'user_id' => auth()->user()->id,
                'value' => $request->value
            ]);
            $lunchPerson = request()->get('persons');
            foreach ($lunchPerson as $person) {
                $person["lunch_id"] = $lunch->id;
                LunchPerson::create($person);
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function activateOrInactivate( Request $request )
    {
        try {
            $state = LunchPerson::find($request->get('id'));
            $state->update($request->all());
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
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
        /* try {
            $lunch = $request->get('')
        } catch (\Throwable $th) {
            //throw $th;
        } */
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
