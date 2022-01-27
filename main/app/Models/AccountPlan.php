<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPlan extends Model
{

    protected $fillable = [
        'type_p',
        'type_niif',
        'code',
        'name',
        'niif_code',
        'niif_name',
        'status',
        'accounting_adjustment',
        'close_third',
        'movement',
        'document',
        'base',
        'value',
        'percent',
        'center_cost',
        'exogenous',
        'nature',
        'close_nit',
        'bank',
        'bank_id',
        'nit',
        'report',
        'class_account',
        'account_number',
        'real_percent',
        'niif',
        'account',
        'annual_voucher'
    ];
    /* protected $table = 'account_plan'; */
	public function balance()
	{
		return $this->hasOne(AccountPlanBalance::class);
	}
}
