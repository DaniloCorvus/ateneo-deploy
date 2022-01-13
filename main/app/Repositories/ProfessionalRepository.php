<?php

namespace App\Repositories;

use App\CustomFacades\ImgUploadFacade;
use App\Models\Person;
use App\Models\Usuario;
use App\Models\WorkContract;
use App\Restriction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function GuzzleHttp\Promise\all;

class ProfessionalRepository
{
    public function paginate()
    {

        $urlBase = DB::table('site_settings')->get(['url', 'folder_functionaries'])->first();

        return   DB::table('people as p')
            ->select(
                'p.id',
                DB::raw(
                    "Concat_ws('', IFNULL(p.image, p.image_blob )) As image"
                ),
                'p.identifier',
                'p.status',
                DB::raw('Concat_ws(" ", p.first_name, p.first_surname ) as full_name'),
                'p.first_surname',
                'p.first_name',
                'pos.name as position',
                'd.name as dependency',
                'c.name as company',
                DB::raw('w.id AS work_contract_id')
            )
            ->leftJoin('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                    join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->leftJoin('companies as c', 'c.id', '=', 'w.company_id')
            ->leftJoin('positions as pos', 'pos.id', '=', 'w.position_id')
            ->leftJoin('dependencies as d', 'd.id', '=', 'pos.dependency_id')
            ->where('people_type_id', 3)
            ->when(request()->get('identifier'), function ($q) {
                $q->where('p.identifier', 'like', request()->get('identifier') . '%');
            })
            ->when(request()->get('name'), function ($q) {
                $q->Where(DB::raw('concat(p.first_name," ",p.first_surname)'), 'LIKE', request()->get('name') . '%');
            })
            ->when(request()->get('status'), function ($q) {
                $q->where('p.status',  request()->get('status'));
            })

            ->when(request()->get('company'), function ($q) {
                $q->where('c.name',  'like', request()->get('company')  . '%');
            })
            ->paginate(request()->get('pageSize'), ['*'], 'page', request()->get('page'));
    }

    public function store()
    {

        $person = Person::find(request()->get('id'));

        if (ImgUploadFacade::validate(request()->get('signature_blob'))) {
            if ($person) ImgUploadFacade::deleteImg($person->signature_blob);
            $infoImg =  ImgUploadFacade::converFromBase64(request()->get('signature_blob'));
            request()->merge([
                'signature_blob' =>  $infoImg['image_blob'],
            ]);
        }

        if (ImgUploadFacade::validate(request()->get('image_blob'))) {
            if ($person) ImgUploadFacade::deleteImg($person->image_blob);
            $infoImg =  ImgUploadFacade::converFromBase64(request()->get('image_blob'));
            request()->merge([
                'image_blob' =>  $infoImg['image_blob'],
            ]);
        }


        $person = Person::updateOrCreate(['id' => request()->get('id')], request()->all());
        // TODO:
        $person->people_type_id = 3;
        $person->save();

        $person->companies()->sync(request()->get('companies'));
        $person->specialities()->sync(request()->get('specialities'));


        WorkContract::create([
            'company_id' => request()->get('company_id'),
            'liquidated' =>  0,
            'salary' => 0,
            'person_id' => $person->id,
            'work_contract_type_id' => request()->get('contract_type_id', 1),
            'date_end' => Carbon::now()->addDecade(),
            'turn_type' => 'Fijo',
        ]);

        Usuario::create([
            'person_id' => $person->id,
            'usuario' => request()->get('identifier'),
            'password' => Hash::make(request()->get('identifier')),
            'change_password' => 1,
        ]);

        if ($person->id) {
            $restrictions = Restriction::Where('person_id', $person->id)->get();
            foreach ($restrictions ?? []  as  $restriction) {
                $restriction->regimentypes()->detach();
                $restriction->contracts()->detach();
                $restriction->companies()->detach();
                $restriction->typeappointments()->detach();
                $restriction->delete();
            }
        }

        $contracts = request()->get('contract', []);

        foreach ($contracts as $contract) {
            $restriction = Restriction::create([
                'person_id' => $person->id,
                'company_id' => $contract['company_id']
            ]);
            $restriction->regimentypes()->sync($contract['regimen_id'] ?? []);
            $restriction->contracts()->sync($contract['contract_id'] ?? []);
            $restriction->companies()->sync($contract['companies_id'] ?? []);
            $restriction->typeappointments()->sync($contract['type_agenda_id'] ?? []);
        }


        return ['id' => $person->id];
    }

    public function show($id)
    {
        return Person::select('*')->with(
            [
                'specialities:id',
                'companies:id',
                'restriction:id,person_id,company_id',
                'restriction.regimentypes:id,name',
                'restriction.company:id,name',
                'restriction.contracts:id,name',
                'restriction.companies:id,name',
                'restriction.typeappointments:id,name'
            ]
        )
            ->find($id);
    }
}
