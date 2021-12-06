<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvisionsPersonPayrollPayment extends Model
{
    use HasFactory;

    protected $fillable = [
	'person_id',
	'payroll_payment_id',
	'severance',
	'severance_interest',
	'prima',
	'vacations',
	'accumulated_vacations',
	'total_provisions'
    ];
}
