<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    //
    use ApiResponser;

    public function index(Request $req)
    {
        # code...
        $data = DB::table('alerts as a')
            ->join('Usuario as u', 'u.id', '=', 'a.user_id')
            ->join('people as pc', 'pc.id', '=', 'u.person_id')
            ->join('people as pr', 'pr.id', '=', 'a.person_id')
            ->select(
                'a.type',
                'a.icon',
                'a.title',
                'a.description',
                'a.url',
                'a.destination_id',
                'a.created_at',
                'pc.image',
                'pr.first_name',
                'pr.first_surname',
            )
            ->when($req->get('person_id'), function ($q, $fill) {
                $q->where('a.person_id', $fill);
            })
            ->orderBy('a.id', 'Desc')
            ->get();
        return $this->success(
            $data
        );
    }
}
