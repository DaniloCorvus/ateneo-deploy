<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
	use ApiResponser;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return $this->success(City::where('state', '=', 'Activo')
		->get(['*', 'id as value', 'name as text']));
	}

	public function paginate()
	{
		return $this->success(
			DB::table('cities as c')
			->select(
				'c.id',
				'c.name',
				'cs.id as country_id',
				'cs.name as country',
				'c.state'
			)
			->when(request()->get('name'), function($q, $fill)
			{
				$q->where('c.name', 'like', '%' . $fill . '%');
			})
			->when(request()->get('country'), function($q, $fill)
			{
				$q->where('c.country_id', 'like', '%' . $fill . '%');
			})
			->join('countries as cs', 'cs.id', '=', 'c.country_id')
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
			$city = City::updateOrCreate(['id' => $request->get('id')],$request->all());
			return ($city->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
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
