<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{


	protected $fillable = [
		'date',
		'interest_type',
		'interest',
		'account_plain_id',
		'value',
		'pay_fees',
		'number_fees',
		'monthly_fee',
		'payment_type',
		'first_payment_date',
		'observation',
		'state',
		'type',
		'user_id',
		'person_id',

		'Mes',
		'Quincena',

		'outstanding_balance'
	];
	public function person()
	{
		return $this->belongsTo(Person::class, 'person_id');
	}
	public function user()
	{
		return $this->belongsTo(Person::class, 'user_id');
	}

	public function fees()
	{
		return $this->hasMany(LoanFee::class);
	}
}
