<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeService extends Model
{
	//

	public function formalities()
	{
		return $this->belongsToMany(TypeService::class);
	}
}
