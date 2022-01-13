<?php

namespace App\Http\Controllers;

use App\Models\BankAccounts;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class BankAccountsController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(BankAccounts::all(['name As text', 'id As value']));
    }


    public function paginate()
    {
        return $this->success(
            BankAccounts::when( Request()->get('name') , function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');
            }
        )
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
            $banksAccount  = BankAccounts::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($banksAccount->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
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
    }
}
