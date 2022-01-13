<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrettyCash extends Model
{

	protected $table = 'pretty_cash';
	protected $fillable = [
		'user_id',
		'person_id',
		'initial_balance',
		'account_plan_id',
		'description'
	];

	function accountPlan()
	{
		return $this->belongsTo(AccountPlan::class);
	}
	function person()
	{
		return $this->belongsTo(Person::class);
	}
	function user()
	{
		return $this->belongsTo(user::class);
	}
}
