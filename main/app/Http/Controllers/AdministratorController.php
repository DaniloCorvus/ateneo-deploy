<?php

namespace App\Http\Controllers;

// use App\CustomModels\Eps;
use App\Models\Administrator;
use App\Traits\ApiResponser;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\Eps;

class AdministratorController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {

              if (!is_null(request()->get('type'))) {
                return $this->success(Eps::orderBy('name', 'DESC')->get(['name As text', 'id As value']));
               }

            $condition = request()->get('type') ?? 1;
            $eps = Administrator::query();
            $eps->where('type', $condition);
            $eps->orderBy('name', 'DESC');
            return response()->success($eps->get(['name As text', 'id As value']));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function paginate()
    {
        try {
            return $this->success(
                Administrator::orderBy('name')->when(request()->get('name'), function (Builder $q) {
                    $q->where('name', 'like', '%' . request()->get('name') . '%');
                })->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
            );
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }

    public function store()
    {
        try {
            $administrator = Administrator::updateOrCreate(['id' => request()->get('id')], request()->all());
            return ($administrator->wasRecentlyCreated === true) ? $this->success('creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }

     public function saveEps()
    {
        try {
            $eps = Eps::updateOrCreate(['id' => request()->get('id')], request()->all());
            return ($eps->wasRecentlyCreated === true) ? $this->success('creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }
}
