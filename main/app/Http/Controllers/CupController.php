<?php

namespace App\Http\Controllers;

use App\Models\Cup;
use Illuminate\Http\Request;


use App\Imports\CupsImport;
use App\Http\Controllers\Controller;
use App\Models\Agendamiento;
use App\Models\Space;
use App\Services\CupService;
use App\Models\Speciality;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class CupController extends Controller
{

    public function __construct(CupService $cupService)
    {
        $this->cupService = $cupService;
    }

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $cups = Cup::query();

        $cups->when(request()->get('search'), function ($q) {
            $q->where(function ($q) {
                $q->where('description', 'Like', '%' . request()->get('search') . '%')
                    ->orWhere('code', 'Like', '%' . request()->get('search') . '%');
            });
        });

        $cups->when(request()->get('speciality'), function ($q) {
            $q->where('speciality', request()->get('speciality'))->get();
        });

        $cups->when(request()->get('space'), function ($q, $spaceId) {
            $space = Space::with('agendamiento.cups:id')->find($spaceId);
            $q->where(function ($q) {
                $q->where('description', 'Like', '%' . request()->get('search') . '%')
                    ->orWhere('code', 'Like', '%' . request()->get('search') . '%');
            })->whereIn('id', $space->agendamiento->cups->pluck('id'));
        });



        return $this->success($cups->get(['id as value',  DB::raw("CONCAT( code, ' - ' ,description) as text")])->take(10));
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
     * @param  \App\Cup  $cup
     * @return \Illuminate\Http\Response
     */
    public function show(Cup $cup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cup  $cup
     * @return \Illuminate\Http\Response
     */
    public function edit(Cup $cup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cup  $cup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cup $cup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cup  $cup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cup $cup)
    {
        //
    }


    public function import()
    {
        Excel::import(new CupsImport, request()->file('file'));
        return redirect('/')->with('success', 'All good!');
    }

    public function storeFromMedical()
    {
        try {
            // $specialities = Speciality::get(['code', 'name']);
            // foreach ($specialities as  $speciality) {
            $this->cups = json_decode($this->cupService->get(), true);
            // if (count($this->cups) > 0) {
            $this->handlerInsertTable($this->cups);
            // }
            // }
            return $this->success('Datos insertados Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function handlerInsertTable($data)
    {
        foreach ($data as  $item) {
            if (gettype($item) != 'array') {
                // dd('Necesitas un array');
            } else {
                $dataFormated = [];
                foreach ($item as $index =>  $value) {
                    if (gettype($value) == 'array') {
                        $this->handlerInsertTableRespaldo($value, $index);
                    } else {
                        if ($index == 'Name') {
                            $dataFormated['description'] = $value;
                        }
                        $dataFormated[customSnakeCase($index)] = $value;
                        // $dataFormated['speciality'] = $speciality;
                    }
                }
            }

            $cup = Cup::firstWhere('code', $dataFormated['code']);
            if (!$cup) {
                Cup::create($dataFormated);
            }
        }
    }


    public function handlerInsertTableRespaldo($data, $table)
    {
        // if (count($data) > 0) {
        //     if ($table != 'EPSs' && $table != 'Interface' && $table != 'Parent' && $table != 'Regional') {
        //         $dataFormated = [];
        //         foreach ($data as $index =>  $value) {
        //             if (gettype($value) == 'array') {
        //                 dd('Otro array');
        //             } else {
        //                 if ($index == 'NAME') {
        //                     $dataFormated['description'] = $value;
        //                 }
        //                 $dataFormated[customSnakeCase($index)] = $value;
        //             }
        //         }
        //     }
        // }
    }
}
