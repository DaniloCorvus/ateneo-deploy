<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RotatingTurn extends Model
{
	//
	protected $fillable = [
		"name",
		"entry_tolerance",
		"leave_tolerance",
		"extra_hours",
		"entry_time",
		"leave_time",
		"launch",
		"launch_time",
		"breack",
		"breack_time",
	];
}
