<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialSecurityPersonPayrollPayment extends Model
{
    protected $fillable = [
        'person_id',
        'payroll_payment_id',
        'health',
        'pension',
        'risks',
        'sena',
        'icbf',
        'compensation_founds',
        'total_social_security',
        'total_parafiscals',
        'total_social_security_parafiscals',
        'created_at',
        'updated_at'
    ];

}
