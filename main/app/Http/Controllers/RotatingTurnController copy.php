<?php

namespace App\Http\Controllers;

use App\Models\RotatingTurn;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Throwable;

class RotatingTurnController extends Controller
{
	use ApiResponser;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
		return $this->success(
			RotatingTurn::
			 with('sunday')
			 ->with('saturday')
			->get(["*","id as value", "name as text", "state"])
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
			return $this->success(RotatingTurn::create($request->all()));
		} catch (Throwable $th) {
			return $this->error($th->getMessage(), 400);
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
		return $this->success(RotatingTurn::find($id));
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
		try {

			$rt = RotatingTurn::find($id);
			$rt->update($request->all());
			return $this->success('Actualizado correctamente');
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 402);
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

	public function changeState($id)
	{
		$turno = RotatingTurn::find($id);

		$turno->state = $turno->state == "Activo" ? "Inactivo" : "Activo";
		$turno->save();
		return $this->success(
			"Turno Actualizado Correctamente",
		);
	}
}
