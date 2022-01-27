<?php

namespace App\Http\Controllers;

use App\Models\DrivingLicenseJob;
use App\Models\Job;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
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
            Job::with([
                'position' => function ($q) {
                    $q->select('name', 'id', 'dependency_id');
                }, 
                'salary_type' => function ($q) {
                    $q->select('id', 'name');
                },
                'position.dependency' => function ($q) {
                    $q->select('name', 'id');
                },
                'municipality' => function ($q) {
                    $q->select('name', 'id', 'department_id');
                },
                'work_contract_type' => function ($q) {
                    $q->select('id', 'name');
                },
                'municipality.department' => function ($q) {
                    $q->select('name', 'id');
                }
                ])
                ->whereHas('position', function ($q) {
                    $q->when(request()->get('dependencia'), function ($q, $fill) {
                        $q->where('dependency_id', '=', $fill);
                    });
                })
                ->whereHas('municipality', function ($q) {
                    $q->when(request()->get('municipio'), function ($q, $fill) {
                        $q->where('id', '=', $fill);
                    });
                    $q->when(request()->get('departamento'), function ($q, $fill) {
                        $q->where('department_id', '=', $fill);
                    });
                })
                ->when(request()->get('cargo'), function ($q, $fill) {
                    $q->where('position_id', '=', $fill);
                })
                ->when(request()->get('fecha'), function ($q, $fill) {
                    $q->where('created_at', 'like', '%' . $fill . '%');
                })
                ->when(request()->get('fecha_Inicio'), function ($q, $fill) {
                    $q->where('date_start', 'like', '%' . $fill . '%');
                })
                ->when(request()->get('fecha_Fin'), function ($q, $fill) {
                    $q->where('date_end', 'like', '%' . $fill . '%');
                })
                ->when(request()->get('titulo'), function ($q, $fill) {
                    $q->where('title', 'like', '%' . $fill . '%');
                })
                ->where('state','Activo')
                ->whereDate('date_end','>' , DB::raw('CURDATE()') )
                ->orderBy('id', 'ASC')
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

    public function getPreview()
    {
        $page = Request()->get('page');
        $page = $page ? $page : 1;

        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        return $this->success(
            Job::with([
                'position' => function ($q) {
                    $q->select('name', 'id', 'dependency_id');
                }, 'salary_type' => function ($q) {
                    $q->select('id', 'name');
                },
                'position.dependency' => function ($q) {
                    $q->select('name', 'id');
                },
                'municipality' => function ($q) {
                    $q->select('name', 'id', 'department_id')
                        ->when(request()->get('municipality_id'), function ($q, $fill) {
                            $q->where('id', '=', $fill);
                        });;
                },
                'work_contract_type' => function ($q) {
                    $q->select('id', 'name');
                },
                'municipality.department' => function ($q) {
                    $q->select('name', 'id');
                }
            ])->whereHas('municipality', function ($q) {
                $q->when(request()->get('municipality_id'), function ($q, $fill) {
                    $q->where('id', '=', $fill);
                });
                $q->when(request()->get('department_id'), function ($q, $fill) {
                    $q->where('department_id', '=', $fill);
                });
            })
       
                ->whereHas('position', function ($q) {
                    $q->when(request()->get('dependency_id'), function ($q, $fill) {
                        $q->where('dependency_id', '=', $fill);
                    });
                })
                ->when(request()->get('position'), function ($q, $fill) {
                    $q->where('title', 'like', '%' . $fill . '%');
                })
                /*  ->when(request()->get('dependency_id'), function ($q, $fill) {
                $q->where('id', '=', $fill);
            }) */
            ->where('state','Activo')
            ->whereDate('date_end','>' , DB::raw('CURDATE()') )
                ->orderBy('id', 'DESC')
                ->simplePaginate($pageSize, '*', 'page', $page)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $job = $request->except(["drivingLicenseJob"]);
        $drivingLincenseJob = request()->get('drivingLicenseJob');
        try {
            $jobDB = Job::create($job);
            $jobDB["code"] = "VAC".$jobDB->id;
            $jobDB->save();
            foreach ($drivingLincenseJob as $driving) {
                DrivingLicenseJob::create([
                    'job_id' =>  $jobDB->id,
                    'driving_license_id' => $driving
                ]);
            }
            return $this->success('creacion exitosa');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th->getLine(), $th->getFile(), 500);
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
        return $this->success(
            Job::with([
                'position' => function ($q) {
                    $q->select('name', 'id', 'dependency_id');
                },
                'position.dependency' => function ($q) {
                    $q->select('name', 'id');
                },
                'municipality' => function ($q) {
                    $q->select('name', 'id', 'department_id');
                },
                'municipality.department' => function ($q) {
                    $q->select('name', 'id');
                },
                'work_contract_type' => function ($q) {
                    $q->select('id', 'name');
                },
                'salary_type' => function ($q) {
                    $q->select('id', 'name');
                },
            ])
                ->where('id', $id)
                ->first()
        );
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
    public function setState($id, Request $request)
    {
        try {
            $job = Job::find($id);
            $job->state = $request->get('state');
            $job->save();
            return $this->success('actualizado exitosa');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }
}
