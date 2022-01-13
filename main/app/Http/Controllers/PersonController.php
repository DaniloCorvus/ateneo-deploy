<?php

namespace App\Http\Controllers;

use App\CustomFacades\ImgUploadFacade;
use App\Http\Requests\ProfessionalRequest;
use App\Models\Eps;
use App\Models\FixedTurn;
use App\Models\Person;
use App\Models\Usuario;
use App\Models\WorkContract;
use App\Services\CognitiveService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PersonController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($company = 0, $speciality = 0)
    {
        // TODO: refactor para solo funcionarios?
        if (request()->get('type')) {
            return response()->success(Person::orderBy('first_name')->where('people_type_id', 2)->get(
                ["id as value", DB::raw('CONCAT_WS(" ",first_name,second_name,first_surname) as text ')]
            ));
        }

        $persons = Person::orderBy('first_name')
            ->whereHas('specialties', function ($q) use ($speciality) {
                $q->where('id', $speciality);
            })
            // ->where('to_globo', 1)
            // ->when(request()->get('type-appointment'), function ($q) {
            //     $q->whereHas('restriction.typeappointments', function ($q) {
            //         $q->where('type_appointment_id', request()->get('type-appointment'));
            //     });
            // })
            // ->when(request()->get('contract_id'), function ($q) {
            //     $q->whereHas('restriction.contracts', function ($q) {
            //         $q->where('contract_id', request()->get('contract_id'));
            //     });
            // })
            // ->when(request()->get('regimen_id'), function ($q) {
            //     $q->whereHas('restriction.regimentypes', function ($q) {
            //         $q->where('regimen_type_id', request()->get('regimen_id'));
            //     });
            // })
            ->get(['id As value', DB::raw('concat(first_name, " ", first_surname)  As text')]);
        return response()->success($persons);
    }

    /**
     * Display a listing of the resource paginated.
     *
     * @return \Illuminate\Http\Response
     */


	public function liquidateOrActivate(Request $request, $id)
	{
		try {
		    
			$person = Person::find($id);
			$person->status = $request->status;
			$person->saveOrFail();
			
			return $this->success($person);
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 500);
		}
	}



    public function indexPaginate()
    {

        $data =  request()->merge(json_decode(request()->get('data'), true));

        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.id',
                    'p.identifier',
                    DB::raw(
                        "Concat_ws('', IFNULL(p.image, p.image_blob )) As image"
                    ),
                    'p.status',
                    DB::raw('Concat_ws(" ", p.first_name, p.first_surname ) as full_name'),
                    'p.first_surname',
                    'p.first_name',
                    'pos.name as position',
                    'd.name as dependency',
                    'c.name as company',
                    DB::raw(
                        'w.id AS work_contract_id'
                    )
                )
                ->leftjoin('work_contracts as w', function ($join) {
                    $join->on('p.id', '=', 'w.person_id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->leftjoin('companies as c', 'c.id', '=', 'w.company_id')
                ->leftjoin('positions as pos', 'pos.id', '=', 'w.position_id')
                ->leftjoin('dependencies as d', 'd.id', '=', 'pos.dependency_id')
                // ->where('people_type_id', 2)
                ->when($data->name, function ($q, $fill) {
                    $q->where('p.identifier', 'like', '%' . $fill . '%')
                        ->orWhere(DB::raw('concat(p.first_name," ",p.first_surname)'), 'LIKE', '%' . $fill . '%');
                })
                ->when($data->dependencies, function ($q, $fill) {
                    $q->whereIn('d.id', $fill);
                })
                ->when($data->companies, function ($q, $fill) {
                    $q->whereIn('c.id', $fill);
                })
                ->when($data->status, function ($q, $fill) {
                    $q->whereIn('p.status', $fill);
                })->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function getAll(Request $request)
    {
        # code...
        $data = $request->all();
        return $this->success(
            DB::table("people as p")
                ->select(
                    "p.id",
                    "p.identifier",
                    "p.image",
                    "p.status",
                    "p.full_name",
                    "p.first_surname",
                    "p.first_name",
                    "pos.name as position",
                    "d.name as dependency",
                    "p.id as value",
                    // "p.passport_number",
                    // "p.visa",
                    DB::raw('CONCAT_WS(" ",first_name,first_surname) as text '),
                    "c.name as company",
                    DB::raw("w.id AS work_contract_id"),
                    DB::raw("'Funcionario' AS type")
                )
                ->join("work_contracts as w", function ($join) {
                    $join->on(
                        "p.id",
                        "=",
                        "w.person_id"
                    )->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join("companies as c", "c.id", "=", "w.company_id")
                ->join("positions as pos", "pos.id", "=", "w.position_id")
                ->join("dependencies as d", "d.id", "=", "pos.dependency_id")
                ->where("p.status", "Activo")
                ->when($request->get('dependencies'), function ($q, $fill) {
                    $q->where("d.id", $fill);
                })
                ->get()
        );
    }

    public function basicData($id)
    {
        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.first_name',
                    'p.first_surname',
                    'p.id',
                    DB::raw(
                        "Concat_ws('', IFNULL(p.image, p.image_blob )) As image"
                    ),
                    'p.second_name',
                    'p.second_surname',
                    'w.salary',
                    'w.id as work_contract_id',
                    'p.signature',
                    'p.title'
                )
                ->leftJoin('work_contracts as w', function ($join) {
                    $join->on('p.id', '=', 'w.person_id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function basicDataForm($id)
    {
        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.first_name',
                    'p.first_surname',
                    'p.second_name',
                    'p.second_surname',
                    'p.identifier',
                    'p.image_blob',
                    'p.email',
                    'p.degree',
                    'p.birth_date',
                    'p.sex',
                    'p.marital_status',
                    'p.address',
                    'p.cell_phone',
                    'p.first_name',
                    'p.first_surname',
                    'p.id',
                    DB::raw(
                        "Concat_ws('', IFNULL(p.image, p.image_blob )) As image"
                    ),
                    'p.second_name',
                    'p.second_surname'
                )
                ->LeftJoin('work_contracts as w', function ($join) {
                    $join->on('p.id', '=', 'w.person_id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function salary($id)
    {
        return $this->success(
            DB::table('people as p')
                ->select(
                    'w.date_of_admission',
                    'w.date_end',
                    'w.salary',
                    'wc.name as contract_type',
                    'w.work_contract_type_id',
                    'w.id'
                )
                ->leftJoin('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                    join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->leftJoin('work_contract_types as wc', function ($join) {
                    $join->on('wc.id', '=', 'w.work_contract_type_id');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function updateSalaryInfo(Request $request)
    {
        try {
            $salary = WorkContract::find($request->get('id'));
            $salary->update($request->all());
            $salary->date_of_admission = request()->get('date_of_admission');
            $salary->save();
            return response()->success($salary);
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function afiliation($id)
    {
        try {
            // DB::select('select * from eps where active = ?', [1])
            return $this->success(
                DB::table('people as p')
                    ->select(
                        'p.eps_id',
                        'e.name as eps_name',
                        'p.compensation_fund_id',
                        'c.name as compensation_fund_name',
                        'p.severance_fund_id',
                        's.name as severance_fund_name',
                        'p.pension_fund_id',
                        'pf.name as pension_fund_name',
                        'a.id as arl_id',
                        'a.name as arl_name'
                    )
                    ->leftJoin('eps as e', function ($join) {
                        $join->on('e.id', '=', 'p.eps_id');
                    })
                    ->leftJoin('arl as a', function ($join) {
                        $join->on('a.id', '=', 'p.arl_id');
                    })
                    ->leftJoin('compensation_funds as c', function ($join) {
                        $join->on('c.id', '=', 'p.compensation_fund_id');
                    })
                    ->leftJoin('severance_funds as s', function ($join) {
                        $join->on('s.id', '=', 'p.severance_fund_id');
                    })
                    ->leftJoin('pension_funds as pf', function ($join) {
                        $join->on('pf.id', '=', 'p.pension_fund_id');
                    })
                    ->where('p.id', '=', $id)
                    ->first()
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function updateAfiliation(Request $request, $id)
    {
        try {
            // return response()->success(request()->all());
            $afiliation = Person::find($id);
            // return response()->success( $afiliation);
            $afiliation->update($request->all());
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function fixed_turn()
    {
        try {
            return $this->success(
                FixedTurn::all(['id as value', 'name as text'])
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function epss()
    {
        try {
            return $this->success(
                EPS::all(['name as text', 'id as value'])
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }


    public function updateBasicData(Request $request, $id)
    {
        try {
            $person = Person::find($id);
            if (ImgUploadFacade::validate(request()->get('image'))) {
                ImgUploadFacade::deleteImg($person->image_blob);
                $infoImg =  ImgUploadFacade::converFromBase64(request()->get('image'));
                request()->merge([
                    'image_blob' =>  $infoImg['image_blob'],
                    'image' =>  $infoImg['image']
                ]);
            }

            $person->update($request->all());

            // if (!file_exists($current_path)) {
            //     mkdir($current_path, 0777, true);
            // }

            return response()->json($person);
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(), $th->getLine(), $th->getFile()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $per = [];
        try {

            $personData = $request->get("person");

            $infoImg =  ImgUploadFacade::converFromBase64($personData["image"]);

            $personData["image_blob"]  =  $infoImg['image_blob'];
            $personData["image"]  =  $infoImg['image'];

            $personData["personId"] = null;

            $per = $person = Person::create($personData);
            $contractData = $personData["workContract"];
            $contractData["person_id"] = $person->id;

            WorkContract::create($contractData);

            Usuario::create([
                "person_id" => $person->id,
                "usuario" => $person->identifier,
                "password" => Hash::make($person->identifier),
                "change_password" => 1,
            ]);
            //crear personID
            if ($personData["image"]) {
                $cognitive = new CognitiveService();
                $person->personId = $cognitive->createPerson($person);
                $cognitive->deleteFace($person);

                $person->persistedFaceId = $cognitive->createFacePoints(
                    $person
                );
                $person->save();
                $cognitive->train();
            }

            return $this->success(["id" => $person->id, 'faceCreated' => true]);
        } catch (\Throwable $th) {
            if ($per) {
                return $this->success(["id" => $person->id, 'faceCreated' => false]);
            }
            return $this->error([$th->getMessage(), $th->getLine(), $th->getFile()], 500);
        }


        // try {

        //     $personData = $request->get('person');

        //     // if (ImgUploadFacade::validate(request()->get('image'))) $personData['image'] = ImgUploadFacade::converFromBase64(request()->get('image'));

        //     $personData['personId'] = null;

        //     // $personData = $this->asure($personData['image'], $personData);

        //     $person = Person::create($personData);
        //     $contractData = $personData['workContract'];
        //     $contractData['person_id'] = $person->id;
        //     WorkContract::create($contractData);

        //     Usuario::create([
        //         'person_id' => $person->id,
        //         'usuario' => $person->identifier,
        //         'password' => Hash::make($person->identifier),
        //         'change_password' => 1,
        //     ]);

        //     return $this->success(['id' => $person->id]);
        // } catch (\Throwable $th) {
        //     return $this->error($th->getMessage(), 500);
        // }

        // try {
        //     $personData = $request->get('person');
        //     $person = Person::create($personData);
        //     $contractData = $personData['workContract'];
        //     $contractData['person_id'] = $person->id;
        //     WorkContract::create($contractData);

        //     Usuario::create([
        //         'person_id' => $person->id,
        //         'usuario' => $person->identifier,
        //         'password' => Hash::make($person->identifier),
        //         'change_password' => 1,
        //     ]);
        //     return $this->success(['id' => $person->id]);
        // } catch (\Throwable $th) {
        //     return $this->error($th->getMessage(), 500);
        // }
    }

    public function asure($fully, $atributos)
    {
        if ($fully != '') {

            $atributos['image'] = $fully;
            if ($atributos["personId"] == "0" || $atributos["personId"] == "null"  || $atributos["personId"] == null) {
                try {
                    $parameters = array();
                    $response = Http::accept('application/json')->withHeaders([
                        'Content-Type' => 'application/json',
                        'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey
                    ])->post($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons' . http_build_query($parameters), [
                        "name" => $atributos["first_name"] . " " . $atributos["first_surname"],
                        "userData" => $atributos["identifier"]
                    ]);
                    $res = $response->json();
                    $atributos["personId"] = $res->personId;
                } catch (HttpExceptionInterface $ex) {
                    echo "error: " . $ex;
                }
            }
            if ($atributos["persistedFaceId"] != "0") {
                $parameters = array();
                $response = Http::accept('application/json')->withHeaders([
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey
                ])->post($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces/' . $atributos["persistedFaceId"] . http_build_query($parameters), [
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                ]);
                $res = $response->json();
            }
            $ruta_guardada = $fully;

            try {
                $parameters = array(
                    "detectionModel" => "detection_02"
                );
                $response = Http::accept('application/json')->withHeaders([
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                ])->post($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces' . http_build_query($parameters), [
                    "url" => $ruta_guardada
                ]);
                $resp = $response->json();
                return ($res);
                if (isset($resp->persistedFaceId) && $resp->persistedFaceId != '') {
                    $persistedFaceId = $resp->persistedFaceId;
                    $atributos["persistedFaceId"] = $persistedFaceId;
                } else {
                    if ($resp->error->code == 'InvalidImage') {
                        return response()->json(['message' => 'No se ha encontrado un rostro en la imagen, revise e intente nuevamente'], 400);
                    }
                    return response()->json(['message' => 'Ha ocurrido un error inisperado'], 400);
                }
            } catch (HttpExceptionInterface $ex) {
                echo $ex;
            }
        }
        return $atributos;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        // $person = Person::find($person);
        return response()->success($person, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        //
    }


    public function storeFromGlobho(ProfessionalRequest $request)
    {
        try {
            $person = Person::create($request->all());
            return $this->success(['Professional creado correctamente', $person]);
        } catch (\Throwable $th) {
            return $this->error(['No se pudo crear el professional', $th->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function updateFromGlobho()
    {
        try {

            $person = Person::firstWhere('identifier', request()->input('identifier'));

            if ($person) {

                $person->update(request()->all());

                return $this->success('Professional actualizado correctamente');
            }

            throw new \Exception('No se logró encontrar professional');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
