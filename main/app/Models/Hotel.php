<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

	protected $fillable = [
		'type',
		'name',
		'address',
		'rate',
		'phone',
		'landline',
		'city_id',
		'simple_rate',
		'double_rate',
		'breakfast',
		'accommodation'
	];
	public function city()
	{
		return $this->belongsTo(City::class);
	}
	public function travelExpenses()
	{

		return $this->belongsToMany(TravelExpense::class);
	}
}
