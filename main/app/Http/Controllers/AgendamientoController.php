<?php

namespace App\Http\Controllers;

use App\Models\Agendamiento;
use App\Http\Resources\AgendmientoResource;
use App\Models\Appointment;
use App\Models\Person;
use App\Services\SpaceService;
use App\Models\Space;
use App\HistoryAppointment;
use App\HistoryAgendamiento;
use App\Holiday;
use App\Models\TypeAppointment;
use App\Repositories\AgendamientoRepository;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SubTypeAppointment;

class AgendamientoController extends Controller
{
    use ApiResponser;

    public $events = [];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
    }

    public function indexPaginate()
    {
        $page = Request()->get("page");
        $page = $page ? $page : 1;

        $pageSize = Request()->get("pageSize");
        $pageSize = $pageSize ? $pageSize : 10;

        // $data = Appointment::with('patient')->whereNull('space_id')
        $data = Agendamiento::query();

        $data->when(request()->get("appointmentId"), function ($q) {
            $q->where("type_agenda_id", request()->get("appointmentId"));
        });

        $data->when(request()->get("subappointmentId"), function ($q) {
            $q->where(
                "type_appointment_id",
                request()->get("subappointmentId")
            );
        });

        $data->when(request()->get("speciality"), function ($q) {
            $q->where("speciality_id", request()->get("speciality"));
        });

        $data->when(request()->get("person"), function ($q) {
            $q->where("person_id", request()->get("person"));
        });

        $data->when(request()->get("ipsId"), function ($q) {
            $q->where("eps_id", request()->get("ipsId"));
        });

        $data->when(request()->get("sede_id"), function ($q) {
            $q->where("eps_id", request()->get("sede_id"));
        });

        //PERMISO DINAMICO
        $showAll = request()->get("show_all_data");
        $data->when(($showAll == "false" || !$showAll), function ($q) {
            $q->where("user_id", auth()->user()->id);
        });
        $data->with([
            "person" => function ($q) {
                $q->select(
                    "id",
                    "full_name",
                    "first_surname",
                    "first_name"
                );
            },
            "speciality" => function ($q) {
                $q->select("id", "name");
            },
            "usuario" => function ($q) {
                $q->select("id", "usuario", "person_id");
            },
            "typeAppointment" => function ($q) {
                $q->select("id", "face_to_face");
            },

            "availableSpaces" => function ($q) {
                $q->select("id", "agendamiento_id");
            },
            "spaces" => function ($q) {
                $q->select("id", "agendamiento_id");
            },
            "assignedSpaces" => function ($q) {
                $q->select("id", "agendamiento_id");
            },
            "usuario.person" => function ($q) {
                $q->select(
                    "id",
                    "identifier",
                    "full_name",
                    "first_surname",
                    "first_name",
                    "image_blob As image"
                );
            },
        ]);
        $data->orderBy("created_at", "DESC");
        //$res = $data->get();
        $res = $data->paginate($pageSize, "*", "page", $page);

        return Response()->json($res);
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
    public function store(AgendamientoRepository $agendamientoRepository, Request $request)
    {
        try {
            return $this->success(['message' => 'Agendado correcto :)', 'status' => $agendamientoRepository->store()]);
        } catch (\Throwable $th) {
            return $this->error(['message' => $th->getMessage(), $th->getLine(), $th->getFile()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agendamiento  $agendamiento
     * @return \Illuminate\Http\Response
     */

    // public function show($agendamiento)

    public function show($person_id)
    {
        return $this->success(
            Space::where("person_id", $person_id)
                /*->where("status", true)*/
                ->get([
                    "id",
                    "hour_start As start",
                    "hour_end As end",
                    "status",
                    /*    "backgroundColor", */
                    "className",
                    DB::raw('
                        CASE
                            WHEN state = "Cancelado" THEN "#d9534f"
                            WHEN status = 0 THEN "#5cb85c"
                            WHEN status = 1 THEN "#0275d8"

                        END
                        AS backgroundColor
                        '),
                    DB::raw('Concat_ws(":",
                        CASE
                            WHEN state = "Cancelado" THEN "Espacio Cancelado"
                            WHEN status = 0 THEN "Espacio Agendado"
                            WHEN status = 1 THEN "Espacio Disponible"
                        END, type)
                        AS title
                        '),
                ])
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agendamiento  $agendamiento
     * @return \Illuminate\Http\Response
     */
    public function edit(Agendamiento $agendamiento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agendamiento  $agendamiento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agendamiento $agendamiento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Agendamiento  $agendamiento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agendamiento $agendamiento)
    {
        //

    }

    public  function cancel(Request $req)
    {
        try {

            $agendamiento = Agendamiento::find($req->id);

            $spaces = DB::table('spaces')
                ->where('agendamiento_id', '=', $req->id)
                ->where("status", '=', '0')
                ->where('state', '=', 'Activo')
                ->first();

            if ($spaces) {
                throw new Exception("La agenda tiene espacios ocupados");
            }

            $agendamiento->state = 'Cancelada';
            $agendamiento->save();

            $affected = DB::table('spaces')
                ->where('agendamiento_id', $req->id)
                ->update(['state' => 'Cancelado']);

            HistoryAgendamiento::create([
                'agendamiento_id' =>  $req->id,
                'user_id' => auth()->user()->id,
                'description' => 'Agenda cancelada completamente',
                'icon' => 'ri-close-circle-line'
            ]);
            return $this->success('Se ha cancelado la agenda satisfactoriamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function showDetail($id)
    {
        $data = Agendamiento::query();
        $data->with([
            "person" => function ($q) {
                $q->select(
                    "id",
                    "identifier",
                    "full_name",
                    "first_surname",
                    "first_name"
                );
            },
            "location" => function ($q) {
                $q->select("*");
            },
            "company" => function ($q) {
                $q->select("id", "name");
            },
            "speciality" => function ($q) {
                $q->select("id", "name");
            },
            "typeAppointment" => function ($q) {
                $q->select("id", "name", "face_to_face", "icon");
            },
            "subTypeAppointment" => function ($q) {
                $q->select("id", "name");
            },
            "usuario" => function ($q) {
                $q->select("id", "usuario", "person_id");
            },
            "usuario.person" => function ($q) {
                $q->select(
                    "id",
                    "identifier",
                    "full_name",
                    "first_surname",
                    "first_name"
                );
            },
            "spaces" => function ($q) {
                $q->select(
                    "id",
                    "agendamiento_id",
                    "hour_start As start",
                    "hour_end As end",
                    "status",
                    "className",
                    "state",

                    DB::raw('
                    CASE
                        WHEN state = "Cancelado" THEN "#d9534f"
                        WHEN status = 0 THEN "#5cb85c"
                        WHEN status = 1 THEN "#0275d8"

                    END
                    AS backgroundColor
                    '),
                    DB::raw('
                    CASE
                        WHEN state = "Cancelado" THEN "Espacio Cancelado"
                        WHEN status = 0 THEN "Espacio Agendado"
                        WHEN status = 1 THEN "Espacio Disponible"

                    END
                    AS title
                    ')

                )->orderBy('id', 'Asc');
            },

            "history" => function ($q) {
                $q->select(
                    "id",
                    "user_id",
                    "agendamiento_id",
                    "icon",
                    "created_at",
                    "description"
                );
            },
            "history.usuario" => function ($q) {
                $q->select("id", "usuario", "person_id");
            },
            "history.usuario.person" => function ($q) {
                $q->select(
                    "id",
                    "identifier",
                    "full_name",
                    "first_surname",
                    "first_name"
                );
            },
        ]);
        return $this->success($data->find($id));
    }


    public function cancellAgenda()
    {

        try {
            $params = request()->get('params');

            if (!isset($params['fecha_inicio']) || !isset($params['fecha_fin']) ||  !isset($params['id'])) {
                throw new Exception("Debe completar los campos correctamente");
            }

            $dateStart = Carbon::parse($params['fecha_inicio']);
            $dateEnd = Carbon::parse($params['fecha_fin']);
            $agendamiento = $params['id'];

            DB::table('spaces')
                ->where('agendamiento_id', $agendamiento)
                ->where("status",  1)
                ->whereDate('hour_start', '>=', $dateStart)
                ->whereDate('hour_end', '<=', $dateEnd)
                // ->get();
                ->update(['state' => 'Cancelado']);

            HistoryAgendamiento::create([
                'agendamiento_id' =>  $agendamiento,
                'user_id' => auth()->user()->id,
                'description' => 'Agenda cancelada desde ' . $dateStart . ' Hasta ' .  $dateEnd,
                'icon' => 'ri-close-circle-line'
            ]);

            return $this->success('Se ha cancelado la agenda satisfactoriamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
