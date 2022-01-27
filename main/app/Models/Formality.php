<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TypeService;

class Formality extends Model
{
	//
	protected $table = 'formalities';
	public function typeServices()
	{
		return $this->belongsToMany(TypeService::class);
	}
}
