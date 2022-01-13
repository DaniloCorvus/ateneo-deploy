<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
  //
  use ApiResponser;


  public function index(Request $request)
  {
    return   $this->success(
      Dependency::when($request->get('group_id'), function ($q, $p) {
        $q->where('group_id', $p);
      })
        ->get(['id as value', 'name as text'])
    );
  }

  public function store(Request $request)
  {
    try {
      Dependency::updateOrCreate( ['id' => $request->get('id')],$request->all());
      return $this->success('creado con exito');
    } catch (\Throwable $th) {
      return $this->error($th->getMessage(), 500);
    }
  }


  public function dependencies()
  {
    return $this->success(
      Dependency::all(['id as value', 'name as text'])
    );
  }

}
