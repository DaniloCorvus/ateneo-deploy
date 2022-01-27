<?php

namespace App\Http\Controllers;

use App\Models\AccountPlan;
use App\Traits\ApiResponser;
use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountPlanController extends Controller
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
			AccountPlan::when(request()->get('name'), function ($q, $fill) {
				$q->where('name', 'like', '%' . $fill . '%');
			})
				->when(request()->get('code'), function ($q, $fill) {
					$q->where('code', 'like', '%' . $fill . '%');
				})
				->when(request()->get('niif_code'), function ($q, $fill) {
					$q->where('niif_code', 'like', '%' . $fill . '%');
				})
				->when(request()->get('niif_name'), function ($q, $fill) {
					$q->where('niif_name', 'like', '%' . $fill . '%');
				})
				->when(request()->get('status'), function ($q, $fill) {
					$q->where('status', 'like', '%' . $fill . '%');
				})
				->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
		);
	}

	public function accountPlan()
	{
		return $this->success(
			DB::table('account_plans as a')
			->select(
				'a.id',
				'a.percent',
				'a.center_cost',
				DB::raw('concat(a.code," - ",a.name) as code'),
				DB::raw('concat(a.niif_code," - ",a.niif_name) as niif_code')
			)
			->get()
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
			$accountPlan  = AccountPlan::updateOrCreate(['id' => $request->get('id')], $request->all());
			return ($accountPlan->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
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

	public function listBalance()
	{
		return $this->success(AccountPlan::with('balance')

			->get(['*', DB::raw("concat(name,' - ', code) as text, id as value")]));
	}
	public function list()
	{
		try {
			$query = ' SELECT id, CONCAT(code," - ",name) as code, center_cost, percent
                FROM account_plans WHERE CHAR_LENGTH( code ) > 5 ';
			return $this->success(DB::select($query));
			//code...
		} catch (\Throwable $th) {
			//throw $th;
			return $this->error($th->getMessage(), 500);
		}
	}
}
