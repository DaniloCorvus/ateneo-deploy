<?php

namespace App\Http\Controllers;

use App\Models\Taxi;
use App\Models\TaxiCity;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxiControlller extends Controller
{
	use ApiResponser;
	/**
	 *
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		try {
			return $this->success(Taxi::with(
				[
					'taxiCities' => function ($q) {
						$q->select('*');
					},
					'taxiCities.city' => function ($q) {
						$q->select('*');
					}
				]
			)->get());
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 400);
		}
	}

	public function paginate()
	{
		return $this->success(
			DB::table('taxis as t')
			->select(
				't.id',
				't.route',
				'tc.type',
				// 'c.name as city',
				// 'c.id as city_id',
				'tc.value',
				'tc.taxi_id'
			)
			->when( request()->get('tipo'), function ($q, $fill)
			{
				$q->where('type','like','%'.$fill.'%');
			})
			->join('taxi_cities as tc', 'tc.taxi_id', '=', 't.id')
			// ->join('cities as c', 'c.id',  '=','tc.city_id')
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
			$taxi = Taxi::create(['route' => $request->route]);
			TaxiCity::create([
				'type' => $request->type,
				'taxi_id' => $taxi->id,
				'city_id' => $request->city_id,
				'value' => $request->value
			]);
			return $this->success('Creado Con éxito');
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
		try {
			Taxi::find($id)->update(['route' => $request->route]);
			TaxiCity::where('taxi_id', $id)->update([
				'type' => $request->type,
				'city_id' => $request->city_id,
				'value' => $request->value,
				'taxi_id' => $request->taxi_id
			]);
			return $this->success('Actualizado Con éxito');
		} catch (\Throwable $th) {
			return $this->error([$th->getMessage(), $th->getLine(), $th->getFile()], 500);
		}
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
