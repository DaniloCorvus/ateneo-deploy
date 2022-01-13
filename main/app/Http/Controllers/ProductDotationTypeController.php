<?php

namespace App\Http\Controllers;

use App\Models\ProductDotationType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductDotationTypeController extends Controller
{
    //
    use ApiResponser;

    public function index()
    {
        return $this->success(
            ProductDotationType::all()
        );
    }

    public function store(Request $req)
    {
        try {
            ProductDotationType::create($req->all());
            return $this->success('Creado exitoso');
        } catch (\Throwable $th) {

            return $this->error($th->getMessage(), 500);
        }
    }




}
