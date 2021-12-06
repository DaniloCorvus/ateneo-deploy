<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonPayrollPayment extends Model
{
    protected $fillable = [
        'person_id',
        'payroll_payment_id',
        'worked_days',
        'salary',
        'transportation_assistance',
        'retentions_deductions',
        'net_salary' 
    ];

    public function person(){
        return $this->belongsTo(Person::class);
    }
}
