<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPaymentConfiguration extends Model
{

    protected $fillable = [
        'calculate_work_disability',
        'pay_deductions',
        'recurring_payment',
        'payment_transport_subsidy',
        'affects_transportation_subsidy',
        'pay_vacations',
        'company_id'
    ];
}
