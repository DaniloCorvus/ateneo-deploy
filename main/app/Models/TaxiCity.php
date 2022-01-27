<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxiCity extends Model
{

	protected $fillable = [
		'type',
		'taxi_id',
		'city_id',
		'value'
	];
	public function city()
	{
		return $this->belongsTo(City::class);
	}
	public function taxi()
	{
		return $this->belongsTo(Taxi::class);
	}
}
