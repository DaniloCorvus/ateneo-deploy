<?php

namespace App\Http\Controllers;

use App\Models\PrettyCash;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PrettyCashController extends Controller
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
		$data = PrettyCash::with(
			[
				'person' => function ($q) {
					$q->select('id', 'identifier', 'first_name', 'first_surname');
				},

				'user' => function ($q) {
					$q->select('id', 'person_id')
						->with(['person' => function ($q) {
							$q->select('id', 'identifier', 'first_name', 'first_surname');
						}]);
				},
				'accountPlan'  => function ($q) {
					$q->select('*')->with('balance');
				}
			]
		)
			->get();

		return $this->success($data);
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
			$data = $request->all();
			$data["user_id"] = auth()->user()->id;
			PrettyCash::create($data);

			return $this->success('creado con éxito');
		} catch (\Throwable $th) {
			return $this->errorResponse($th->getMessage(), 402);
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
