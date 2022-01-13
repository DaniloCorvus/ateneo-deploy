<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\RotatingTurnHour;
use App\Services\RotatingHourService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RotatingTurnHourController extends Controller
{
    use ApiResponser;
    public function getDatosGenerales($semana)
    {
        $companies = Company::when(
            Request()->get("company_id"),
            function ($q, $fill) {
                $q->where("id", $fill);
            }
        )->get();

        foreach ($companies as $keyG => &$company) {
            $groups = DB::table("groups")
                ->when(Request()->get('group_id'), function ($q, $fill) {
                    $q->where('id', $fill);
                })->get(["id", "name"]);
            $groupsGlob = [];
            foreach ($groups as $key => &$group) {
                $dependencies = DB::table("dependencies")
                    ->when(Request()->get('dependency_id'), function ($q, $fill) {
                        $q->where('id', $fill);
                    })
                    ->where("group_id", $group->id)
                    ->get()->toArray();

                if (!$dependencies) {
                    unset($groups[$key]);
                    continue;
                }

                $depLocal = [];
                foreach ($dependencies as $key2 => &$dependency) {
                    $dependency->people = RotatingHourService::getPeople(
                        $dependency->id,
                        $company->id
                    );

                    /*     dd($dependency->people); */
                    if (!$dependency->people) {
                        /* dd($dependency);
                        var_dump('em', $dependency['people']);
                        exit; */
                        unset($dependencies[$key2]);
                        continue;
                    }

                    foreach ($dependency->people as &$person) {
                        $person->fixed_turn_hours = RotatingHourService::getHours(
                            $person->id,
                        );
                    }
                    $depLocal[] = $dependency;
                }


                if (!$depLocal) {
                    unset($groups[$key]);
                    continue;
                }
                $group->dependencies = [];
                $group->dependencies = $depLocal;

                $groupsGlob[] = $group;
            }
            if ($groups->isEmpty()) {
                unset($companies[$keyG]);
                continue;
            }
            $company->groups = $groupsGlob;
        }
        return $this->success($companies);
    }

    public function store()
    {
        /*  $atributos = request()->validate([
            'funcionario_id' => 'required',
            'turno_rotativo_id' => 'required',
            'fecha' => 'required',
            'numero_semana' => 'required',
        ]);
 */
        $data = Request()->all();
        try {
            foreach ($data as $atributos) {
                $fecha = $atributos['date'];
                $personId = $atributos['person_id'];
                $horarioExistente = RotatingTurnHour::where('date', $fecha)->where('person_id', $personId)->first();

                if ($horarioExistente) {
                    $horarioExistente->update($atributos);
                }else{
                    RotatingTurnHour::create($atributos);
                }
            }
            return $this->success('Horario Asignado correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(),401);
        }
    }


    public function update($id){
        dd($id);
    }
}
