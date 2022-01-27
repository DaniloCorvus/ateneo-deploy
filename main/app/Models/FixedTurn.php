<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedTurn extends Model
{
	//

	protected $fillable = [
		"entry_tolerance",
		"extra_hours",
		"leave_tolerance",
		"name",
	];
	public function horariosTurnoFijo()
	{
		return $this->hasMany(FixedTurnHour::class);
	}

	public function diariosTurnoFijo()
	{
		return $this->hasMany(DiarioTurnoFijo::class);
	}
}
