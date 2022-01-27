<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeveranceFundRequest;
use App\Models\SeveranceFund;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class SeveranceFundController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  $this->success( SeveranceFund::all(['id as value','name as text']) );
    }

    public function paginate()
    {
        return $this->success(
            SeveranceFund::orderBy('name')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function store(SeveranceFundRequest $request) {
        try {
            $severanceFund = SeveranceFund::updateOrCreate( [ 'id'=> $request->get('id') ], $request->all() );
            return ($severanceFund->wasRecentlyCreated) ? $this->success('creado con exito') : $this->success('actualizado con exito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }

}
