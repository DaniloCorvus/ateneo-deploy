<?php

namespace App\Http\Controllers;

use App\Models\CompensationFund;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CompensationFundController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  $this->success( CompensationFund::all(['id as value','name as text']) );
    }
}
