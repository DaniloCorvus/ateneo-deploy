<?php

namespace App\Http\Controllers;

use App\Models\Formality;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeServiceController extends Controller
{
	//
	use ApiResponser;
	public function allByFormality($formality)
	{
		return $this->success(Formality::find($formality)->typeServices);
	}
}
