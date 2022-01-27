<?php

namespace App\Http\Controllers;

use App\Models\ThirdPartyPerson;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThirdPartyPersonController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            DB::table('third_party_people as tp')
            ->select(
                'tp.name', 'tp.observation', 'tp.cell_phone', 'tp.email', 'tp.position',
                DB::raw('concat(t.first_name," ",t.first_surname) as third_party'),
            )
            ->when(request()->get('third'), function($q, $fill)
            {
                $q->where(DB::raw('concat(t.first_name," ",t.first_surname)'), 'like','%'.$fill.'%');
            })
            ->when(request()->get('name'), function($q, $fill)
            {
                $q->where('tp.name', 'like','%'.$fill.'%');
            })
            ->join('third_parties as t', 't.id', '=', 'tp.third_party_id')
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
        //
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
    }
}
