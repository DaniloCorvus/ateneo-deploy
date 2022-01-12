<?php

namespace App\Http\Controllers;

use App\Models\Cie10;
use App\Http\Resources\Cie10Resource;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class Cie10Controller extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $cies10 = Cie10::query();
            $cies10->when(request()->input('search') != '', function ($q) {
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . request()->input('search') . '%')
                        ->orWhere('code', 'like', '%' . request()->input('search') . '%');
                });
            });

            return $this->success(Cie10Resource::collection($cies10->take(10)->get()));
            // return $this->success(Cie10Resource::collection(Cie10::get()));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cie10  $cie10
     * @return \Illuminate\Http\Response
     */
    public function show(Cie10 $cie10)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cie10  $cie10
     * @return \Illuminate\Http\Response
     */
    public function edit(Cie10 $cie10)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cie10  $cie10
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cie10 $cie10)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cie10  $cie10
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cie10 $cie10)
    {
        //
    }
}
