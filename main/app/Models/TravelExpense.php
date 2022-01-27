<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpense extends Model
{

	protected $guarded = ['id'];

	public function destiny()
	{
		return $this->belongsTo(City::class, 'destinity_id');
	}
	public function origin()
	{
		return $this->belongsTo(City::class, 'origin_id');
	}
	public function user()
	{
		return $this->belongsTo(Usuario::class)->with('person');
	}
	public function person()
	{
		return $this->belongsTo(Person::class);
	}
	public function transports()
	{
		return $this->hasMany(TravelExpenseTransport::class);
	}
	public function feedings()
	{
		return $this->hasMany(TravelExpenseFeeding::class);
	}
	public function hotels()
	{
		return $this->belongsToMany(Hotel::class, 'travel_expense_hotels')->withPivot('who_cancels', 'n_night', 'breakfast', 'total', 'breakfast', 'rate', 'accommodation');
	}
	public function expenseTaxiCities()
	{
		return $this->hasMany(TravelExpenseTaxiCity::class);
	}
}
