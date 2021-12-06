<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use App\Traits\ApiResponser;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class EpsController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            $condition = request()->get('type') ?? 1;
            $eps = Eps::query();
            $eps->where('type', $condition);
            $eps->orderBy('name', 'DESC')->get(['name As text', 'id As value']);
            return $this->success($eps->get());
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function paginate()
    {
        try {
            return $this->success(
                Eps::orderBy('name')->when(request()->get('name'), function (Builder $q) {
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
            $Eps = Eps::updateOrCreate(['id' => request()->get('id')], request()->all());
            return ($Eps->wasRecentlyCreated === true) ? $this->success('creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }
}
